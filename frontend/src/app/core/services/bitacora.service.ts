import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Bitacora } from '../models/bitacora.model';

@Injectable({ providedIn: 'root' })
export class BitacoraService {
  private readonly http = inject(HttpClient);

  listar(): Observable<Bitacora[]> {
    return this.http.get<Bitacora[]>(`${environment.apiUrl}/bitacora`);
  }
}
