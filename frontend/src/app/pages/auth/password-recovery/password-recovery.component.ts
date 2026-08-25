import { Component, inject, signal } from "@angular/core";
import { FormBuilder, ReactiveFormsModule, Validators } from "@angular/forms";
import { RouterLink } from "@angular/router";
import { AuthService } from "../../../core/services/auth.service";

@Component({
    selector: "app-password-recovery",
    templateUrl: "./password-recovery.component.html",
    imports: [ReactiveFormsModule, RouterLink],
})

export class PasswordRecoveryComponent {
    private readonly fb = new FormBuilder();
    private readonly authService = inject(AuthService);

    form = this.fb.group({
        usuario: ['', [Validators.required, Validators.email]],
    });

    cargando = signal(false);
    error = signal<string | null>(null);
    exito = signal(false);

    submit(): void {
        if (this.form.invalid) {
            this.form.markAllAsTouched(); // muestra los errores de validación en todos los campos del formulario
            return;
        }

        this.cargando.set(true);
        this.error.set(null);
        this.exito.set(false);

        this.authService.recuperarPassword(this.form.value.usuario!).subscribe({
            next: () => {
                this.exito.set(true);
                this.cargando.set(false);
                this.form.reset();
            },
            error: (respuesta) => {
                this.cargando.set(false);
                this.error.set(
                    respuesta?.error?.errors?.usuario?.[0] ??
                        respuesta?.error?.message ??
                        'No se pudo procesar la solicitud.',
                );
            },
        });
    }

}