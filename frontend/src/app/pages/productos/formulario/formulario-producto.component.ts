import { Component, EventEmitter, Input, OnChanges, Output } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Producto } from '../../../core/models/producto.model';

export interface ProductoFormValue {
  nombre: string;
  marca: string;
  precio: string;
}

@Component({
  selector: 'app-formulario-producto',
  imports: [ReactiveFormsModule],
  templateUrl: './formulario-producto.component.html',
})
export class FormularioProductoComponent implements OnChanges {
  @Input() producto: Producto | null = null; // null = modo alta, con valor = modo edición
  @Input() cargando = false;
  @Input() error: string | null = null;
  @Output() guardar = new EventEmitter<ProductoFormValue>(); //
  @Output() cancelar = new EventEmitter<void>();

  private readonly fb = new FormBuilder();

  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(255)]],
    marca: ['', [Validators.required, Validators.maxLength(255)]],
    precio: ['', [Validators.required, Validators.pattern(/^\d{1,3}(\.\d{1,2})?$/)]], // Máximo 3 dígitos enteros
  });

  ngOnChanges(): void {
    if (this.producto) {
      this.form.patchValue({
        nombre: this.producto.nombre,
        marca: this.producto.marca,
        precio: this.producto.precio,
      });
    } else {
      this.form.reset();
    }
  }

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.guardar.emit(this.form.getRawValue() as ProductoFormValue);
  }
}
