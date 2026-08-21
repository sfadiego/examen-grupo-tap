/* global use, db */
const database = "examen_grupo_tap";
use(database);

db.createCollection("producto", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["codigo", "nombre", "precio", "marca"],
      properties: {
        codigo: { bsonType: "string" },
        nombre: { bsonType: "string" },
        precio: { bsonType: "decimal" },
        marca: { bsonType: "string" },
        created_at: { bsonType: "date" },
        updated_at: { bsonType: "date" }
      }
    }
  }
});

db.producto.createIndex({ codigo: 1 }, { unique: true });

db.createCollection("usuario", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["codigo", "usuario", "nombre", "foto", "password"],
      properties: {
        codigo: { bsonType: "string" },
        usuario: { bsonType: "string" },
        nombre: { bsonType: "string" },
        foto: { bsonType: "string" },
        telefono: { bsonType: ["string", "null"] },
        password: { bsonType: "string" },
        perfil_id: { bsonType: "array" },
        created_at: { bsonType: "date" },
        updated_at: { bsonType: "date" }
      }
    }
  }
});

db.usuario.createIndex({ codigo: 1 }, { unique: true });
db.usuario.createIndex({ usuario: 1 }, { unique: true });

db.createCollection("perfil", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["codigo", "nombre"],
      properties: {
        codigo: { bsonType: "string" },
        nombre: { bsonType: "string" },
        usuario_id: { bsonType: "array" },
        seccion_id: { bsonType: "array" },
        created_at: { bsonType: "date" },
        updated_at: { bsonType: "date" }
      }
    }
  }
});

db.perfil.createIndex({ codigo: 1 }, { unique: true });

db.createCollection("seccion", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["codigo", "nombre"],
      properties: {
        codigo: { bsonType: "string" },
        nombre: { bsonType: "string" },
        perfil_id: { bsonType: "array" },
        created_at: { bsonType: "date" },
        updated_at: { bsonType: "date" }
      }
    }
  }
});

db.seccion.createIndex({ codigo: 1 }, { unique: true });

db.createCollection("bitacora", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["modelo", "modelo_id", "datos_anteriores", "datos_nuevos"],
      properties: {
        modelo: { bsonType: "string" },
        modelo_id: { bsonType: "objectId" },
        datos_anteriores: { bsonType: "object" },
        datos_nuevos: { bsonType: "object" },
        usuario_id: { bsonType: "objectId" },
        created_at: { bsonType: "date" }
      }
    }
  }
});

db.bitacora.createIndex({ modelo: 1, modelo_id: 1 });
