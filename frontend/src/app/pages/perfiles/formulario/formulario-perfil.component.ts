import { Component, EventEmitter, Input, OnChanges, Output } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Seccion } from '../../../core/models/seccion.model';
import { PerfilConSecciones } from '../../../core/services/perfil.service';

export interface PerfilFormValue {
  nombre: string;
  seccionIds: string[];
}

@Component({
  selector: 'app-formulario-perfil',
  imports: [ReactiveFormsModule],
  templateUrl: './formulario-perfil.component.html',
})
export class FormularioPerfilComponent implements OnChanges {
  @Input() perfil: PerfilConSecciones | null = null; // null = modo alta, con valor = modo edición
  @Input() secciones: Seccion[] = []; // catálogo completo, para las opciones
  @Input() cargando = false;
  @Input() error: string | null = null;
  @Output() guardar = new EventEmitter<PerfilFormValue>();
  @Output() cancelar = new EventEmitter<void>();

  private readonly fb = new FormBuilder();

  form = this.fb.group({
    nombre: ['', [Validators.required, Validators.maxLength(255)]],
  });

  seccionesSeleccionadas = new Set<string>();

  ngOnChanges(): void {
    if (this.perfil) {
      this.form.patchValue({ nombre: this.perfil.nombre });
      this.seccionesSeleccionadas = new Set(this.perfil.secciones.map((s) => s.id));
    } else {
      this.form.reset();
      this.seccionesSeleccionadas = new Set();
    }
  }

  toggleSeccion(id: string, marcada: boolean): void {
    if (marcada) {
      this.seccionesSeleccionadas.add(id);
    } else {
      this.seccionesSeleccionadas.delete(id);
    }
  }

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.guardar.emit({
      nombre: this.form.value.nombre!,
      seccionIds: Array.from(this.seccionesSeleccionadas),
    });
  }
}
