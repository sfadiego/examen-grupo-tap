export interface Bitacora {
  id: string;
  modelo: string;
  modelo_id: string;
  datos_anteriores: Record<string, unknown>;
  datos_nuevos: Record<string, unknown>;
  usuario_id: string | null;
  created_at: string;
  updated_at: string;
}
