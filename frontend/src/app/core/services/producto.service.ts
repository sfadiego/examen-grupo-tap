import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Producto } from '../models/producto.model';

type ProductoDatos = { nombre: string; marca: string; precio: string };

@Injectable({ providedIn: 'root' })
export class ProductoService {
  private readonly http = inject(HttpClient);
  private readonly baseUrl = `${environment.apiUrl}/productos`;

  listar(): Observable<Producto[]> {
    return this.http.get<Producto[]>(this.baseUrl);
  }

  crear(valor: ProductoDatos): Observable<Producto> {
    return this.http.post<Producto>(this.baseUrl, valor);
  }

  actualizar(id: string, valor: ProductoDatos): Observable<Producto> {
    return this.http.put<Producto>(`${this.baseUrl}/${id}`, valor);
  }

  eliminar(id: string): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/${id}`);
  }

  // responseType 'blob': la respuesta es el archivo binario, no JSON.
  descargarPdf(): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/pdf`, { responseType: 'blob' });
  }

  descargarExcel(): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/excel`, { responseType: 'blob' });
  }
}
