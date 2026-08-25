import { Component, EventEmitter, Input, OnChanges, Output } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Usuario } from '../../../core/models/usuario.model';

export interface UsuarioFormValue {
  nombre: string;
  usuario: string;
  telefono: string | null;
  foto: File | null;
}

// Misma cota que StoreUsuarioRequest ('foto' => 'max:2048' kilobytes).
const FOTO_MAX_BYTES = 2048 * 1024;

@Component({
  selector: 'app-formulario-usuario',
  imports: [ReactiveFormsModule],
  templateUrl: './formulario-usuario.component.html',
})
export class FormularioUsuarioComponent implements OnChanges {
  @Input() usuario: Usuario | null = null; // null = modo alta, con valor = modo edición
  @Input() cargando = false;
  @Input() error: string | null = null;
  @Output() guardar = new EventEmitter<UsuarioFormValue>();
  @Output() cancelar = new EventEmitter<void>();

  private readonly fb = new FormBuilder();

  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(255)]],
    usuario: ['', [Validators.required, Validators.email, Validators.maxLength(255)]],
    // Formato E.164: + seguido de 8 a 15 dígitos (misma regla que el backend).
    telefono: ['', [Validators.pattern(/^\+\d{8,15}$/)]],
  });

  fotoSeleccionada: File | null = null;
  errorFoto: string | null = null;

  ngOnChanges(): void {
    if (this.usuario) {
      this.form.patchValue({
        nombre: this.usuario.nombre,
        usuario: this.usuario.usuario,
        telefono: this.usuario.telefono ?? '',
      });
    } else {
      this.form.reset();
    }

    this.fotoSeleccionada = null;
    this.errorFoto = null;
  }

  onFotoChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    const archivo = input.files?.[0] ?? null;

    if (archivo && archivo.size > FOTO_MAX_BYTES) {
      this.fotoSeleccionada = null;
      this.errorFoto = 'La foto no debe superar 2MB.';
      input.value = '';
      return;
    }

    this.fotoSeleccionada = archivo;
    this.errorFoto = null;
  }

  submit(): void {
    // La foto solo es obligatoria al crear; al editar se conserva la actual.
    if (!this.usuario && !this.fotoSeleccionada) {
      this.errorFoto = 'La foto de perfil es requerida.';
    }

    if (this.form.invalid || this.errorFoto) {
      this.form.markAllAsTouched();
      return;
    }

    this.guardar.emit({
      nombre: this.form.value.nombre!,
      usuario: this.form.value.usuario!,
      telefono: this.form.value.telefono || null,
      foto: this.fotoSeleccionada,
    });
  }
}
