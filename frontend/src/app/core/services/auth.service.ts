import { HttpClient } from '@angular/common/http';
import { Injectable, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { Observable, tap } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Usuario } from '../models/usuario.model';

const TOKEN_KEY = 'auth_token';
const USUARIO_KEY = 'auth_usuario';

export interface Credenciales {
  usuario: string;
  password: string;
}

interface LoginResponse {
  usuario: Usuario;
  token: string;
}

//@Injectable({ providedIn: 'root' }) Indica que este servicio estará disponible en toda la aplicación 
// y se inyectará automáticamente en los componentes que lo necesiten.
@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly http = inject(HttpClient);
  private readonly router = inject(Router);

  private readonly _usuario = signal<Usuario | null>(this.leerUsuarioGuardado());
  readonly usuario = this._usuario.asReadonly();

  get token(): string | null {
    return localStorage.getItem(TOKEN_KEY);
  }

  tieneAcceso(seccion: string): boolean {
    return this._usuario()?.secciones?.includes(seccion) ?? false;
  }

  login(credenciales: Credenciales): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${environment.apiUrl}/login`, credenciales)
      .pipe(
        tap(({ usuario, token }) => {
          localStorage.setItem(TOKEN_KEY, token);
          localStorage.setItem(USUARIO_KEY, JSON.stringify(usuario));
          this._usuario.set(usuario);
        }),
      );
  }

  recuperarPassword(usuario: string): Observable<{ message: string }> {
    return this.http.post<{ message: string }>(`${environment.apiUrl}/password/recuperar`, { usuario });
  }

  logout(): void {
    this.http.post(`${environment.apiUrl}/logout`, {}).subscribe({
      next: () => this.finalizarSesion(),
      error: () => this.finalizarSesion(),
    });
  }

  private finalizarSesion(): void {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USUARIO_KEY);
    this._usuario.set(null);
    this.router.navigateByUrl('/login');
  }

  private leerUsuarioGuardado(): Usuario | null {
    const guardado = localStorage.getItem(USUARIO_KEY);
    return guardado ? JSON.parse(guardado) : null;
  }
}
