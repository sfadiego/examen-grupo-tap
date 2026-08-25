import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-modal',
  templateUrl: './modal.component.html',
})
export class ModalComponent {
  @Input() abierto = false;
  @Input() titulo = '';
  @Output() cerrar = new EventEmitter<void>();

  onBackdropClick(): void {
    this.cerrar.emit();
  }
}
