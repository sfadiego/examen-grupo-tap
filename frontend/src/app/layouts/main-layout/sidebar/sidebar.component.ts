import { Component, inject } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

interface NavItem {
  etiqueta: string;
  ruta: string;
  seccion?: string; // nombre exacto de SeccionEnum; sin esto, el item es visible para cualquiera
}

@Component({
  selector: 'app-sidebar',
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './sidebar.component.html',
})
export class SidebarComponent {
  private readonly authService = inject(AuthService);

  // Rutas previstas para el sistema; algunas aún no tienen pantalla creada.
  navItems: NavItem[] = [
    { etiqueta: 'Consulta de Productos', ruta: '/productos', seccion: 'Consulta de productos' },
    { etiqueta: 'Consulta de Usuarios', ruta: '/usuarios', seccion: 'Consulta de usuarios' },
    { etiqueta: 'Perfiles de usuarios', ruta: '/perfiles', seccion: 'Perfiles de usuarios' },
    { etiqueta: 'Bitacora', ruta: '/bitacora' },
  ];

  mostrar(item: NavItem): boolean {
    return !item.seccion || this.authService.tieneAcceso(item.seccion);
  }

  cerrarSesion(): void {
    this.authService.logout();
  }
}
