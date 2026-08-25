import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Perfil } from '../models/perfil.model';
import { Seccion } from '../models/seccion.model';

export type PerfilConSecciones = Perfil & { secciones: Seccion[] };

@Injectable({ providedIn: 'root' })
export class PerfilService {
  private readonly http = inject(HttpClient);
  private readonly baseUrl = `${environment.apiUrl}/perfiles`;

  listar(): Observable<Perfil[]> {
    return this.http.get<Perfil[]>(this.baseUrl);
  }

  // El listado no trae las secciones relacionadas; el detalle sí (Perfil::with('secciones')).
  obtener(id: string): Observable<PerfilConSecciones> {
    return this.http.get<PerfilConSecciones>(`${this.baseUrl}/${id}`);
  }

  crear(valor: { nombre: string }): Observable<Perfil> {
    return this.http.post<Perfil>(this.baseUrl, valor);
  }

  actualizar(id: string, valor: { nombre: string }): Observable<Perfil> {
    return this.http.put<Perfil>(`${this.baseUrl}/${id}`, valor);
  }

  eliminar(id: string): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  // sync() en el backend: reemplaza por completo las secciones del perfil (no las agrega).
  asignarSecciones(id: string, seccionIds: string[]): Observable<PerfilConSecciones> {
    return this.http.post<PerfilConSecciones>(`${this.baseUrl}/${id}/secciones`, {
      seccion_ids: seccionIds,
    });
  }

  descargarPdf(): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/pdf`, { responseType: 'blob' });
  }

  descargarExcel(): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/excel`, { responseType: 'blob' });
  }
}
