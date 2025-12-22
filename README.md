# APIDIAN - API Facturación Electrónica DIAN Colombia

API REST para facturación electrónica según normativa DIAN Colombia UBL 2.1

## Características

- ✅ Laravel 5.8 + PHP 7.4
- ✅ Facturación Electrónica UBL 2.1
- ✅ Firma Digital XAdES-EPES
- ✅ Integración completa con DIAN
- ✅ Docker optimizado para producción
- ✅ Instalación automatizada

## Instalación Rápida con Docker

```bash
# Clonar repositorio
git clone https://github.com/elierrosero-del/apidian.git
cd apidian

# Ejecutar instalación automatizada
chmod +x install.sh
sudo ./install.sh
```

## Requisitos

- Ubuntu 20.04 LTS o superior
- Docker y Docker Compose
- Certificado digital DIAN (.p12)
- Resolución de facturación DIAN

## Configuración

1. Acceder a la API: `http://tu-servidor`
2. Configurar empresa DIAN
3. Subir certificado digital
4. Configurar resoluciones
5. ¡Listo para facturar!

## Documentación

- [Comandos de instalación manual](Comandos%20Instalacion%20API%202024%20Linux%20Ubuntu%2020.txt)
- [Colección Postman](ApiDianV2.1.postman_collection.json)

## Soporte

Para soporte técnico, contactar al desarrollador.

---

**Desarrollado para facturación electrónica en Colombia según normativa DIAN**