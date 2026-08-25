import { Routes } from '@angular/router';
import { seccionGuard } from './core/guards/seccion.guard';
import { AuthLayoutComponent } from './layouts/auth-layout/auth-layout.component';
import { MainLayoutComponent } from './layouts/main-layout/main-layout.component';
import { ForbiddenComponent } from './pages/forbidden/forbidden.component';
import { LoginComponent } from './pages/auth/login/login.component';
import { PasswordRecoveryComponent } from './pages/auth/password-recovery/password-recovery.component';
import { ListadoProductosComponent } from './pages/productos/listado/listado-productos.component';
import { ListadoUsuariosComponent } from './pages/usuarios/listado/listado-usuarios.component';
import { ListadoPerfilesComponent } from './pages/perfiles/listado/listado-perfiles.component';
import { ListadoBitacoraComponent } from './pages/bitacora/listado/listado-bitacora.component';

export const routes: Routes = [
  {
    path: '',
    component: AuthLayoutComponent,
    children: [
      { path: '', redirectTo: 'login', pathMatch: 'full' },
      { path: 'login', component: LoginComponent },
      { path: 'password-recovery', component: PasswordRecoveryComponent },
    ],
  },
  {
    path: '',
    component: MainLayoutComponent,
    children: [
      {
        path: 'productos',
        component: ListadoProductosComponent,
        canActivate: [seccionGuard('Consulta de productos')],
      },
      {
        path: 'usuarios',
        component: ListadoUsuariosComponent,
        canActivate: [seccionGuard('Consulta de usuarios')],
      },
      {
        path: 'perfiles',
        component: ListadoPerfilesComponent,
        canActivate: [seccionGuard('Perfiles de usuarios')],
      },
      { path: 'bitacora', component: ListadoBitacoraComponent },
      { path: 'sin-acceso', component: ForbiddenComponent },
    ],
  },
];
