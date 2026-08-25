import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { ApplicationConfig, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';

import { routes } from './app.routes';
import { authInterceptor } from './core/interceptors/auth.interceptor';

export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes), // Proporciona el enrutador para la navegación entre rutas en la aplicación
    provideHttpClient(withInterceptors([authInterceptor])), // Cliente HTTP + adjunta el Bearer token a cada request
  ]
};
