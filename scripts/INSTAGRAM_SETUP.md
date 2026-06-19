# Conector de Instagram por hashtag (#RedObservatoriosBoyacá)

Muestra automáticamente en el **home** las publicaciones de `@secplaneacionboyaca`
que lleven el hashtag **#RedObservatoriosBoyacá**.

El código ya está listo. Solo falta obtener las credenciales de Meta **una vez** y
pegarlas en `website/config/instagram.local.php`.

---

## 1. Requisitos previos (una sola vez)

1. **La cuenta de Instagram debe ser Business o Creator.**
   En la app de Instagram: *Configuración → Cuenta → Cambiar a cuenta profesional*.
2. **Vincular esa cuenta a una página de Facebook** de la Secretaría.
   (Instagram → Configuración → *Centro de cuentas* → agregar la página de Facebook).

## 2. Crear la app en Meta for Developers

1. Entra a <https://developers.facebook.com/> con la cuenta que administra la página.
2. *Mis aplicaciones → Crear aplicación → tipo "Empresa"*.
3. Agrega el producto **Instagram Graph API**.

## 3. Obtener el ID de la cuenta y el token

La forma más rápida es el **Explorador de la Graph API**
(<https://developers.facebook.com/tools/explorer/>):

1. Selecciona tu app y genera un *User Access Token* con estos permisos:
   `instagram_basic`, `pages_show_list`, `business_management`.
2. Consulta tu página y su cuenta de Instagram vinculada:
   ```
   GET /me/accounts                       → copia el id de la página
   GET /{id-pagina}?fields=instagram_business_account
                                          → copia "instagram_business_account.id"
   ```
   Ese número es tu **`ig_user_id`**.
3. **Convierte el token a uno de larga duración** (~60 días):
   ```
   GET https://graph.facebook.com/v21.0/oauth/access_token
       ?grant_type=fb_exchange_token
       &client_id={APP_ID}
       &client_secret={APP_SECRET}
       &fb_exchange_token={TOKEN_CORTO}
   ```
   Copia el `access_token` que devuelve.

## 4. Configurar el sitio

1. Copia la plantilla:
   `website/config/instagram.local.php.example` → `website/config/instagram.local.php`
2. Rellena `access_token` e `ig_user_id`, y deja `'enabled' => true`.
   (Este archivo está en `.gitignore`: el token no se sube al repositorio.)

## 5. Probar

```
C:\xampp\php\php.exe C:\xampp\htdocs\Observatorio2026\scripts\sync_instagram.php
```
Debe imprimir algo como:
`Sincronización OK · 4 publicaciones con #RedObservatoriosBoyacá · 4 nuevas · 0 actualizadas`

Luego recarga el home: las publicaciones aparecerán en la franja de Instagram.

## 6. Automatizar (diario)

Programador de tareas de Windows, o pídeselo a Claude Code para que cree la tarea:
- Programa: `C:\xampp\php\php.exe`
- Argumentos: `C:\xampp\htdocs\Observatorio2026\scripts\sync_instagram.php`
- Frecuencia: diaria (p. ej. 7:00 a. m.)

## Notas

- El token de larga duración **caduca cada ~60 días**; hay que regenerarlo (paso 3.3).
  Para producción se puede automatizar la renovación.
- El conector lee las publicaciones **de tu propia cuenta** y filtra por el hashtag
  en el texto. No usa el "hashtag search" general (más restringido), por lo que solo
  muestra publicaciones de `@secplaneacionboyaca`, que es justo lo que se quiere.
- Si el token es inválido o caduca, el conector **no rompe el sitio**: simplemente no
  actualiza y registra el error; el home sigue mostrando lo último sincronizado.
