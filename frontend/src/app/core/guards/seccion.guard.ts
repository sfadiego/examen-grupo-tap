import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export function seccionGuard(seccion: string): CanActivateFn {
  return () => {
    const authService = inject(AuthService);
    const router = inject(Router);

    return authService.tieneAcceso(seccion) ? true : router.parseUrl('/sin-acceso');
  };
}
