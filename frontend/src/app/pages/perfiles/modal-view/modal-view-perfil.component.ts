import { DatePipe } from '@angular/common';
import { Component, Input } from '@angular/core';
import { PerfilConSecciones } from '../../../core/services/perfil.service';

@Component({
  selector: 'app-modal-view-perfil',
  imports: [DatePipe],
  templateUrl: './modal-view-perfil.component.html',
})
export class ModalViewPerfilComponent {
  @Input() perfil: PerfilConSecciones | null = null;
}
