import { DatePipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { Perfil } from '../../../core/models/perfil.model';
import { Seccion } from '../../../core/models/seccion.model';
import { PerfilConSecciones, PerfilService } from '../../../core/services/perfil.service';
import { SeccionService } from '../../../core/services/seccion.service';
import { ModalComponent } from '../../../ui/components/modal/modal.component';
import { FormularioPerfilComponent, PerfilFormValue } from '../formulario/formulario-perfil.component';
import { ModalViewPerfilComponent } from '../modal-view/modal-view-perfil.component';

@Component({
  selector: 'app-listado-perfiles',
  imports: [DatePipe, ModalComponent, FormularioPerfilComponent, ModalViewPerfilComponent],
  templateUrl: './listado-perfiles.component.html',
})
export class ListadoPerfilesComponent implements OnInit {
  private readonly perfilService = inject(PerfilService);
  private readonly seccionService = inject(SeccionService);

  perfiles = signal<Perfil[]>([]);
  cargando = signal(true);
  error = signal<string | null>(null);

  seccionesDisponibles = signal<Seccion[]>([]);

  // Alta / edición
  formularioAbierto = signal(false);
  modoEdicion = signal(false);
  perfilEnEdicion = signal<PerfilConSecciones | null>(null); // null mientras carga o en modo alta
  cargandoDetalleFormulario = signal(false);
  errorDetalleFormulario = signal<string | null>(null);
  cargandoFormulario = signal(false);
  errorFormulario = signal<string | null>(null);

  // Detalle (Ver)
  detalleAbierto = signal(false);
  perfilSeleccionado = signal<PerfilConSecciones | null>(null);
  cargandoDetalle = signal(false);
  errorDetalle = signal<string | null>(null);

  // Eliminar
  eliminandoId = signal<string | null>(null);
  errorAccion = signal<string | null>(null);

  descargando = signal<'pdf' | 'excel' | null>(null);

  ngOnInit(): void {
    this.cargarPerfiles();
    this.seccionService.listar().subscribe({
      next: (secciones) => this.seccionesDisponibles.set(secciones),
    });
  }

  cargarPerfiles(): void {
    this.cargando.set(true);
    this.error.set(null);

    this.perfilService.listar().subscribe({
      next: (perfiles) => {
        this.perfiles.set(perfiles);
        this.cargando.set(false);
      },
      error: () => {
        this.error.set('No se pudo cargar el listado de perfiles.');
        this.cargando.set(false);
      },
    });
  }

  nuevoPerfil(): void {
    this.modoEdicion.set(false);
    this.perfilEnEdicion.set(null);
    this.errorDetalleFormulario.set(null);
    this.formularioAbierto.set(true);
  }

  editar(perfil: Perfil): void {
    this.modoEdicion.set(true);
    this.perfilEnEdicion.set(null);
    this.formularioAbierto.set(true);
    this.cargandoDetalleFormulario.set(true);
    this.errorDetalleFormulario.set(null);

    // El listado no trae las secciones actuales del perfil; hay que pedir el detalle.
    this.perfilService.obtener(perfil.id).subscribe({
      next: (detalle) => {
        this.perfilEnEdicion.set(detalle);
        this.cargandoDetalleFormulario.set(false);
      },
      error: () => {
        this.errorDetalleFormulario.set('No se pudo cargar el perfil.');
        this.cargandoDetalleFormulario.set(false);
      },
    });
  }

  cerrarFormulario(): void {
    this.formularioAbierto.set(false);
    this.perfilEnEdicion.set(null);
    this.cargandoFormulario.set(false);
    this.errorFormulario.set(null);
  }

  guardarPerfil(valor: PerfilFormValue): void {
    const perfilActual = this.perfilEnEdicion();

    this.cargandoFormulario.set(true);
    this.errorFormulario.set(null);

    const guardarNombre = perfilActual
      ? this.perfilService.actualizar(perfilActual.id, { nombre: valor.nombre })
      : this.perfilService.crear({ nombre: valor.nombre });

    guardarNombre.subscribe({
      next: (perfilGuardado) => {
        // Segundo paso: asignar las secciones seleccionadas al perfil ya creado/actualizado.
        this.perfilService.asignarSecciones(perfilGuardado.id, valor.seccionIds).subscribe({
          next: (perfilConSecciones) => {
            if (perfilActual) {
              this.perfiles.update((lista) =>
                lista.map((p) => (p.id === perfilActual.id ? perfilConSecciones : p)),
              );
            } else {
              this.perfiles.update((lista) => [...lista, perfilConSecciones]);
            }
            this.cerrarFormulario();
          },
          error: (respuesta) => {
            this.cargandoFormulario.set(false);
            this.errorFormulario.set(
              respuesta?.error?.message ?? 'El perfil se guardó, pero no se pudieron asignar las secciones.',
            );
          },
        });
      },
      error: (respuesta) => {
        this.cargandoFormulario.set(false);
        this.errorFormulario.set(
          respuesta?.error?.errors?.nombre?.[0] ??
            respuesta?.error?.message ??
            (perfilActual ? 'No se pudo actualizar el perfil.' : 'No se pudo crear el perfil.'),
        );
      },
    });
  }

  visualizar(perfil: Perfil): void {
    this.detalleAbierto.set(true);
    this.cargandoDetalle.set(true);
    this.errorDetalle.set(null);
    this.perfilSeleccionado.set(null);

    this.perfilService.obtener(perfil.id).subscribe({
      next: (detalle) => {
        this.perfilSeleccionado.set(detalle);
        this.cargandoDetalle.set(false);
      },
      error: () => {
        this.errorDetalle.set('No se pudo cargar el detalle del perfil.');
        this.cargandoDetalle.set(false);
      },
    });
  }

  cerrarDetalle(): void {
    this.detalleAbierto.set(false);
    this.perfilSeleccionado.set(null);
    this.errorDetalle.set(null);
  }

  eliminar(perfil: Perfil): void {
    const confirmado = confirm(`¿Eliminar el perfil "${perfil.nombre}"?`);
    if (!confirmado) return;

    this.errorAccion.set(null);
    this.eliminandoId.set(perfil.id);

    this.perfilService.eliminar(perfil.id).subscribe({
      next: () => {
        this.perfiles.update((lista) => lista.filter((p) => p.id !== perfil.id));
        this.eliminandoId.set(null);
      },
      error: (respuesta) => {
        this.eliminandoId.set(null);
        this.errorAccion.set(respuesta?.error?.message ?? 'No se pudo eliminar el perfil.');
      },
    });
  }

  descargarPdf(): void {
    this.descargando.set('pdf');
    this.errorAccion.set(null);

    this.perfilService.descargarPdf().subscribe({
      next: (blob) => {
        this.descargarArchivo(blob, 'perfiles.pdf');
        this.descargando.set(null);
      },
      error: () => {
        this.errorAccion.set('No se pudo descargar el PDF.');
        this.descargando.set(null);
      },
    });
  }

  descargarExcel(): void {
    this.descargando.set('excel');
    this.errorAccion.set(null);

    this.perfilService.descargarExcel().subscribe({
      next: (blob) => {
        this.descargarArchivo(blob, 'perfiles.xlsx');
        this.descargando.set(null);
      },
      error: () => {
        this.errorAccion.set('No se pudo descargar el Excel.');
        this.descargando.set(null);
      },
    });
  }

  private descargarArchivo(blob: Blob, nombre: string): void {
    const url = URL.createObjectURL(blob);
    const enlace = document.createElement('a');
    enlace.href = url;
    enlace.download = nombre;
    enlace.click();
    URL.revokeObjectURL(url);
  }
}
