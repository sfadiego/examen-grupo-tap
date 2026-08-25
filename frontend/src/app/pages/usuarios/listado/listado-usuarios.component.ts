import { DatePipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { Usuario } from '../../../core/models/usuario.model';
import { UsuarioConPerfiles, UsuarioService } from '../../../core/services/usuario.service';
import { ModalComponent } from '../../../ui/components/modal/modal.component';
import { FormularioUsuarioComponent, UsuarioFormValue } from '../formulario/formulario-usuario.component';
import { ModalViewUsuarioComponent } from '../modal-view/modal-view-usuario.component';

@Component({
  selector: 'app-listado-usuarios',
  imports: [DatePipe, ModalComponent, FormularioUsuarioComponent, ModalViewUsuarioComponent],
  templateUrl: './listado-usuarios.component.html',
})
export class ListadoUsuariosComponent implements OnInit {
  private readonly usuarioService = inject(UsuarioService);

  usuarios = signal<Usuario[]>([]);
  cargando = signal(true);
  error = signal<string | null>(null);

  // Alta / edición
  formularioAbierto = signal(false);
  usuarioEnEdicion = signal<Usuario | null>(null); // null = alta, con valor = edición
  cargandoFormulario = signal(false);
  errorFormulario = signal<string | null>(null);

  // Detalle (Ver)
  detalleAbierto = signal(false);
  usuarioSeleccionado = signal<UsuarioConPerfiles | null>(null);
  cargandoDetalle = signal(false);
  errorDetalle = signal<string | null>(null);

  // Eliminar
  eliminandoId = signal<string | null>(null);
  errorAccion = signal<string | null>(null);

  descargando = signal<'pdf' | 'excel' | null>(null);

  ngOnInit(): void {
    this.cargarUsuarios();
  }

  cargarUsuarios(): void {
    this.cargando.set(true);
    this.error.set(null);

    this.usuarioService.listar().subscribe({
      next: (usuarios) => {
        this.usuarios.set(usuarios);
        this.cargando.set(false);
      },
      error: () => {
        this.error.set('No se pudo cargar el listado de usuarios.');
        this.cargando.set(false);
      },
    });
  }

  nuevoUsuario(): void {
    this.usuarioEnEdicion.set(null);
    this.formularioAbierto.set(true);
  }

  editar(usuario: Usuario): void {
    this.usuarioEnEdicion.set(usuario);
    this.formularioAbierto.set(true);
  }

  cerrarFormulario(): void {
    this.formularioAbierto.set(false);
    this.usuarioEnEdicion.set(null);
    this.cargandoFormulario.set(false);
    this.errorFormulario.set(null);
  }

  guardarUsuario(valor: UsuarioFormValue): void {
    const usuarioActual = this.usuarioEnEdicion();

    const formData = new FormData();
    formData.append('nombre', valor.nombre);
    formData.append('usuario', valor.usuario);
    if (valor.telefono) {
      formData.append('telefono', valor.telefono);
    }
    if (valor.foto) {
      formData.append('foto', valor.foto);
    }

    this.cargandoFormulario.set(true);
    this.errorFormulario.set(null);

    const peticion = usuarioActual
      ? this.usuarioService.actualizar(usuarioActual.id, formData)
      : this.usuarioService.crear(formData);

    peticion.subscribe({
      next: (usuarioGuardado) => {
        if (usuarioActual) {
          this.usuarios.update((lista) =>
            lista.map((u) => (u.id === usuarioActual.id ? usuarioGuardado : u)),
          );
        } else {
          this.usuarios.update((lista) => [...lista, usuarioGuardado]);
        }
        this.cerrarFormulario();
      },
      error: (respuesta) => {
        this.cargandoFormulario.set(false);
        this.errorFormulario.set(
          respuesta?.error?.errors?.usuario?.[0] ??
          respuesta?.error?.message ??
          (usuarioActual ? 'No se pudo actualizar el usuario.' : 'No se pudo crear el usuario.'),
        );
      },
    });
  }

  visualizar(usuario: Usuario): void {
    this.detalleAbierto.set(true);
    this.cargandoDetalle.set(true);
    this.errorDetalle.set(null);
    this.usuarioSeleccionado.set(null);

    this.usuarioService
      .obtener(usuario.id)
      .subscribe({
        next: (detalle) => {
          this.usuarioSeleccionado.set(detalle);
          this.cargandoDetalle.set(false);
        },
        error: () => {
          this.errorDetalle.set('No se pudo cargar el detalle del usuario.');
          this.cargandoDetalle.set(false);
        },
      });
  }

  cerrarDetalle(): void {
    this.detalleAbierto.set(false);
    this.usuarioSeleccionado.set(null);
    this.errorDetalle.set(null);
  }

  eliminar(usuario: Usuario): void {
    const confirmado = confirm(`¿Eliminar al usuario "${usuario.nombre}"?`);
    if (!confirmado) return;

    this.errorAccion.set(null);
    this.eliminandoId.set(usuario.id);

    this.usuarioService.eliminar(usuario.id).subscribe({
      next: () => {
        this.usuarios.update((lista) => lista.filter((u) => u.id !== usuario.id));
        this.eliminandoId.set(null);
      },
      error: (respuesta) => {
        this.eliminandoId.set(null);
        this.errorAccion.set(respuesta?.error?.message ?? 'No se pudo eliminar el usuario.');
      },
    });
  }

  descargarPdf(): void {
    this.descargando.set('pdf');
    this.errorAccion.set(null);

    this.usuarioService.descargarPdf().subscribe({
      next: (blob) => {
        this.descargarArchivo(blob, 'usuarios.pdf');
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

    this.usuarioService.descargarExcel().subscribe({
      next: (blob) => {
        this.descargarArchivo(blob, 'usuarios.xlsx');
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
