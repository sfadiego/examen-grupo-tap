import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Perfil } from '../models/perfil.model';
import { Usuario } from '../models/usuario.model';

export type UsuarioConPerfiles = Usuario & { perfiles: Perfil[] };

@Injectable({ providedIn: 'root' })
export class UsuarioService {
  private readonly http = inject(HttpClient);
  private readonly baseUrl = `${environment.apiUrl}/usuarios`;

  listar(): Observable<Usuario[]> {
    return this.http.get<Usuario[]>(this.baseUrl);
  }

  obtener(id: string): Observable<UsuarioConPerfiles> {
    return this.http.get<UsuarioConPerfiles>(`${this.baseUrl}/${id}`);
  }

  crear(datos: FormData): Observable<Usuario> {
    return this.http.post<Usuario>(this.baseUrl, datos);
  }

  // PHP no llena $_FILES en requests PUT, subimos el archivo como POST con "_method=PUT"
  actualizar(id: string, datos: FormData): Observable<Usuario> {
    datos.append('_method', 'PUT');
    return this.http.post<Usuario>(`${this.baseUrl}/${id}`, datos);
  }

  eliminar(id: string): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  descargarPdf(): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/pdf`, { responseType: 'blob' });
  }

  descargarExcel(): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/excel`, { responseType: 'blob' });
  }
}
