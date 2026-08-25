import { Component, Input, OnChanges, signal } from '@angular/core';
import { environment } from '../../../../environments/environment';
import { UsuarioConPerfiles } from '../../../core/services/usuario.service';

@Component({
  selector: 'app-modal-view-usuario',
  templateUrl: './modal-view-usuario.component.html',
})
export class ModalViewUsuarioComponent implements OnChanges {
  @Input() usuario: UsuarioConPerfiles | null = null;

  // Si la imagen falla al cargar (URL rota, archivo eliminado, etc.), mostramos el label igual.
  errorImagen = signal(false);

  ngOnChanges(): void {
    this.errorImagen.set(false);
  }

  // El backend devuelve la url como ruta relativa (/storage/...); hay que
  // resolverla contra el origen del backend, no del frontend.
  get fotoUrl(): string | null {
    const foto = this.usuario?.foto_url;
    if (!foto || this.errorImagen()) return null;

    return foto.startsWith('http') ? foto : `${environment.storageUrl}${foto}`;
  }
}
