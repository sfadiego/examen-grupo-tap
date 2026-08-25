import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Seccion } from '../models/seccion.model';

@Injectable({ providedIn: 'root' })
export class SeccionService {
  private readonly http = inject(HttpClient);

  listar(): Observable<Seccion[]> {
    return this.http.get<Seccion[]>(`${environment.apiUrl}/secciones`);
  }
}
