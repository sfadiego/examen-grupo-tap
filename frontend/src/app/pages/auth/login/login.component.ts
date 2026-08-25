import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-login',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './login.component.html',
})

export class LoginComponent {
  private readonly fb = new FormBuilder();
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);

  form = this.fb.group({
    usuario: ['', [Validators.required, Validators.email]],
    password: ['', Validators.required],
  });

  cargando = signal(false);
  error = signal<string | null>(null);

  submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched(); // muestra los errores de validación en todos los campos del formulario
      return;
    }

    this.cargando.set(true);
    this.error.set(null);

    this.authService
      .login({
        usuario: this.form.value.usuario!,
        password: this.form.value.password!,
      })
      .subscribe({
        next: () => this.router.navigateByUrl('/productos'),
        error: () => {
          this.error.set('Usuario o contraseña incorrectos.');
          this.cargando.set(false);
        },
      });
  }
}
