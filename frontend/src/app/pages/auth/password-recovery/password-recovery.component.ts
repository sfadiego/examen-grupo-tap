import { Component } from "@angular/core";
import { FormBuilder, ReactiveFormsModule, Validators } from "@angular/forms";
import { RouterLink } from "@angular/router";

@Component({
    selector: "app-password-recovery",
    templateUrl: "./password-recovery.component.html",
    imports: [ReactiveFormsModule, RouterLink],
})

export class PasswordRecoveryComponent {
    private readonly fb = new FormBuilder();

    form = this.fb.group({
        usuario: ['', [Validators.required, Validators.email]],
    });

    submit(): void {
        if (this.form.invalid) {
            this.form.markAllAsTouched(); // muestra los errores de validación en todos los campos del formulario
            return;
        }


    }

}