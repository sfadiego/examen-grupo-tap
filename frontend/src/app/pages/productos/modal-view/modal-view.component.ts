import { DatePipe, DecimalPipe } from "@angular/common";
import { Component, Input } from "@angular/core";
import { Producto } from "../../../core/models/producto.model";

@Component({
    selector: "app-modal-view",
    imports: [DatePipe, DecimalPipe],
    templateUrl: "./modal-view.component.html",
})
export class ModalViewComponent {
    @Input() producto: Producto | null = null;
}