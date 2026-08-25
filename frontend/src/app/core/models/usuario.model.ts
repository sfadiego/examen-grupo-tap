export interface Usuario {
  id: string;
  codigo: string;
  usuario: string;
  nombre: string;
  foto: string | null;
  foto_url: string | null;
  telefono: string | null;
  created_at: string;
  updated_at: string;
  secciones?: string[]; // solo viene en la respuesta de login/me
}
