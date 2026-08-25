import { DatePipe, DecimalPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { Producto } from '../../../core/models/producto.model';
import { AuthService } from '../../../core/services/auth.service';
import { ProductoService } from '../../../core/services/producto.service';
import { FormularioProductoComponent, ProductoFormValue } from '../formulario/formulario-producto.component';
import { ModalViewComponent } from '../modal-view/modal-view.component';
import { ModalComponent } from '../../../ui/components/modal/modal.component';

//DatePipe -> Se utiliza para formatear fechas en la plantilla del componente.
//DecimalPipe -> Se utiliza para formatear números decimales en la plantilla del componente.
@Component({
  selector: 'app-listado-productos',
  imports: [DatePipe, DecimalPipe, ModalComponent, FormularioProductoComponent, ModalViewComponent],
  templateUrl: './listado-productos.component.html',
})
export class ListadoProductosComponent implements OnInit {
  private readonly productoService = inject(ProductoService);
  readonly authService = inject(AuthService);

  productos = signal<Producto[]>([]);
  cargando = signal(true);
  error = signal<string | null>(null);

  productoSeleccionado = signal<Producto | null>(null);
  formularioAbierto = signal<boolean>(false); // alta/edición
  productoEnEdicion = signal<Producto | null>(null); // null = alta, con valor = edición
  cargandoFormulario = signal(false);
  errorFormulario = signal<string | null>(null);

  eliminandoId = signal<string | null>(null);
  errorAccion = signal<string | null>(null);

  descargando = signal<'pdf' | 'excel' | null>(null);

  ngOnInit(): void {
    this.cargarProductos();
  }

  cargarProductos(): void {
    this.cargando.set(true);
    this.error.set(null);

    this.productoService.listar()
      .subscribe({
        next: (productos) => {
          this.productos.set(productos);
          this.cargando.set(false);
        },
        error: () => {
          this.error.set('No se pudo cargar el listado de productos.');
          this.cargando.set(false);
        },
      });
  }

  nuevoProducto(): void {
    this.productoEnEdicion.set(null);
    this.formularioAbierto.set(true);
  }

  editar(producto: Producto): void {
    this.productoEnEdicion.set(producto);
    this.formularioAbierto.set(true);
  }

  cerrarFormulario(): void {
    this.formularioAbierto.set(false);
    this.productoEnEdicion.set(null);
    this.cargandoFormulario.set(false);
    this.errorFormulario.set(null);
  }

  guardarProducto(valor: ProductoFormValue): void {
    const productoActual = this.productoEnEdicion();

    this.cargandoFormulario.set(true);
    this.errorFormulario.set(null);

    const peticion = productoActual
      ? this.productoService.actualizar(productoActual.id, valor)
      : this.productoService.crear(valor);

    peticion.subscribe({
      next: (productoGuardado) => {
        if (productoActual) {
          this.productos.update((lista) =>
            lista.map((p) => (p.id === productoActual.id ? productoGuardado : p)),
          );
        } else {
          this.productos.update((lista) => [...lista, productoGuardado]);
        }
        this.cerrarFormulario();
      },
      error: (respuesta) => {
        this.cargandoFormulario.set(false);
        this.errorFormulario.set(
          respuesta?.error?.errors?.nombre?.[0] ??
          respuesta?.error?.message ??
          (productoActual ? 'No se pudo actualizar el producto.' : 'No se pudo crear el producto.'),
        );
      },
    });
  }

  visualizar(producto: Producto): void {
    this.productoSeleccionado.set(producto);
  }

  cerrarModal(): void {
    this.productoSeleccionado.set(null);
  }

  eliminar(producto: Producto): void {
    const confirmado = confirm(`¿Eliminar el producto "${producto.nombre}"?`);
    if (!confirmado) return;

    this.errorAccion.set(null);
    this.eliminandoId.set(producto.id);

    this.productoService.eliminar(producto.id).subscribe({
      next: () => {
        this.productos.update((lista) => lista.filter((p) => p.id !== producto.id));
        this.eliminandoId.set(null);
      },
      error: (respuesta) => {
        this.eliminandoId.set(null);
        this.errorAccion.set(
          respuesta?.error?.message ?? 'No se pudo eliminar el producto.',
        );
      },
    });
  }

  descargarPdf(): void {
    this.descargando.set('pdf');
    this.errorAccion.set(null);

    this.productoService.descargarPdf().subscribe({
      next: (blob) => {
        this.descargarArchivo(blob, 'productos.pdf');
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

    this.productoService.descargarExcel().subscribe({
      next: (blob) => {
        this.descargarArchivo(blob, 'productos.xlsx');
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
