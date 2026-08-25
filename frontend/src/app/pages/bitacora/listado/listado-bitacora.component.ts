import { DatePipe, JsonPipe } from '@angular/common';
import { Component, OnInit, inject, signal } from '@angular/core';
import { Bitacora } from '../../../core/models/bitacora.model';
import { BitacoraService } from '../../../core/services/bitacora.service';

@Component({
  selector: 'app-listado-bitacora',
  imports: [DatePipe, JsonPipe],
  templateUrl: './listado-bitacora.component.html',
})
export class ListadoBitacoraComponent implements OnInit {
  private readonly bitacoraService = inject(BitacoraService);

  registros = signal<Bitacora[]>([]);
  cargando = signal(true);
  error = signal<string | null>(null);

  ngOnInit(): void {
    this.cargarBitacora();
  }

  cargarBitacora(): void {
    this.cargando.set(true);
    this.error.set(null);

    this.bitacoraService.listar().subscribe({
      next: (registros) => {
        this.registros.set(registros);
        this.cargando.set(false);
      },
      error: () => {
        this.error.set('No se pudo cargar la bitácora.');
        this.cargando.set(false);
      },
    });
  }
}
