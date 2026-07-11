---
name: livewire-laravel-design
description: Design and build UI for tiendax ("Sublimar Yamer"), a Laravel 12 + Livewire retail POS app being migrated to a Flux-UI-first, controller-free architecture. Use this whenever the user asks to build, style, redesign, or add a page, module, form, modal, table, card, dashboard, KPI widget, or any visual/frontend piece in this project — even if they just say "hazme una vista para X" or "agrega un botón/tarjeta/formulario" without mentioning design explicitly. Covers the new standard: Flux UI components (`<flux:input>`, `<flux:table>`, `<flux:modal>`, `<flux:badge>`...) with default Flux theming, the Index/Header/Table/Form Livewire component pattern per module, the new `components.layouts.app.sidebar` Flux app-shell layout, and how the still-unmigrated legacy pages (dashboard, categorías, clientes, ventas, movimientos, reportes) use a different hand-rolled Tailwind MD3 system for now. Always consult this before writing new Blade/Livewire markup.
---

# Diseño en tiendax (Sublimar Yamer)

**El proyecto está en medio de una migración de arquitectura.** Hasta hace poco
todo era controladores + Blade + Tailwind hecho a mano (paleta Material Design
3 custom). Ahora el rumbo confirmado por el usuario es: **cada módulo se
reescribe como componentes Livewire (Index/Header/Table/Form) usando Flux UI
con su theming por defecto** (no la paleta MD3 custom). El módulo piloto,
**Producto**, ya está migrado y es la referencia canónica — cópialo, no
inventes un patrón nuevo.

- **Sistema nuevo (Flux UI + Livewire)** — úsalo para cualquier módulo nuevo o
  que estés migrando: `app/Livewire/Admin/{Modulo}/`, layout
  `components.layouts.app.sidebar`, componentes `<flux:*>` con su theming
  default (zinc/accent, no MD3). Ver "El patrón Index/Header/Table/Form" abajo.
- **Sistema legado (Tailwind MD3 a mano)** — todavía vive en dashboard,
  categorías, subcategorías, clientes, ventas, movimientos, reportes, búsqueda
  global (`x-layouts.app`, `x-layouts.sales`). No lo copies para código nuevo;
  documentado solo por si tocas esas vistas antes de que se migren — tokens de
  color/tipografía en [references/tokens.md](references/tokens.md), recetas de
  componentes en [references/patterns.md](references/patterns.md).
- Las vistas de auth (`livewire/auth/login.blade.php`) usan un tema oscuro
  custom aparte (`zinc-950`/`indigo-600`, layout `x-layouts.auth.simple`) — no
  se toca en esta migración, es un caso aparte.

## El módulo piloto: Producto

`app/Livewire/Admin/Producto/` es la referencia viva de cómo debe verse
**cualquier módulo nuevo**. Cópialo literalmente para el siguiente módulo:

```
app/Livewire/Admin/{Modulo}/
  {Modulo}Index.php     — página completa, #[Layout('components.layouts.app.sidebar', ['title' => '...'])]
  {Modulo}Header.php    — título + botón de acción principal (dispara evento)
  {Modulo}Table.php     — filtros (#[Url]), flux:table, paginación, editar/eliminar
  {Modulo}Form.php      — flux:modal, crear/editar dual (id null = crear)

resources/views/livewire/admin/{modulo}/
  {modulo}-index.blade.php
  {modulo}-header.blade.php
  {modulo}-table.blade.php
  {modulo}-form.blade.php
```

Comunicación entre componentes vía eventos Livewire (`$this->dispatch(...)` +
`#[On(...)]`), no props descendentes forzadas — mismo patrón en todos los
módulos: `abrir-formulario-{modulo}` (Header y Table lo disparan para
crear/editar, Form lo escucha), `{modulo}-guardado` / `{modulo}-eliminado`
(Form/Table lo disparan al guardar/borrar, Table e Index lo escuchan para
refrescar tabla y KPIs).

Rutas: una sola ruta de página (`Route::get('/productos', ProductoIndex::class)`);
no hay rutas separadas para create/edit — todo vive en el modal del Form.
`show` (detalle de solo lectura) y endpoints JSON tipo `buscar` siguen siendo
controladores normales si no tienen formulario que migrar.

**Antes de migrar un módulo**, grep por el nombre de sus rutas viejas
(`{modulo}.create`, `{modulo}.edit`, etc.) en TODO `resources/views`, no solo
en las vistas del propio módulo — otras páginas enlazan hacia ellas
(breadcrumbs, "ver más", resultados de búsqueda) y hay que redirigirlas al
patrón `route('{modulo}.index', ['editar' => $id])` /
`['crear' => 1]`, que el `{Modulo}Index::mount()` debe leer para abrir el
modal correspondiente automáticamente. Esto se me pasó una vez en el piloto
(el botón "Editar" de `productos/show.blade.php` quedó apuntando a una ruta
borrada) — un grep completo antes de borrar rutas lo evita.

## El layout Flux: `components.layouts.app.sidebar`

Nuevo app-shell completo en `resources/views/components/layouts/app/sidebar.blade.php`,
construido con componentes estructurales de Flux (`flux:sidebar`,
`flux:navlist`, `flux:header`, `flux:main`, `flux:dropdown`, `flux:profile`,
`flux:menu`). A diferencia de `x-layouts.app` (legado), este sí usa el
pipeline de Vite (`@include('partials.head')` → `@vite([...])` con
`resources/css/app.css`, que ya importa `flux/dist/flux.css`) y **requiere
`@fluxScripts` antes de `</body>`** — sin eso los componentes Flux
interactivos (modal, dropdown, select) no funcionan porque su JS/Alpine no se
carga. Nunca uses Tailwind por CDN en una página que use componentes Flux:
`flux.css` depende de directivas `@theme`/`@layer` de Tailwind v4 que un CDN
no puede compilar — los componentes se ven rotos (sin color de acento, sin
transiciones de modal) si lo intentas.

Después de tocar CSS/Tailwind config, corre `npm run build` (o mantén
`npm run dev` corriendo) — este layout no tiene JIT en el navegador como el
legado.

Usa `#[Layout('components.layouts.app.sidebar', ['title' => 'Mi Página'])]` en
el componente de página (el prop `title` alimenta el `<title>` vía
`partials/head.blade.php`).

## Componentes Flux disponibles (Free, ya instalado — v2.14, sin Pro)

Todos verificados en `vendor/livewire/flux/stubs/resources/views/flux/`:
`input`, `select` (+ `select.option`), `textarea`, `checkbox`, `radio`,
`switch`, `button` (+ `button.group`), `badge`, `card`, `modal` (+
`modal.close`, `modal.trigger`), `table` (+ `table.columns`, `table.column`
con `sortable`, `table.rows`, `table.row`, `table.cell`), `callout`,
`heading`, `subheading`, `text`, `avatar`, `dropdown`, `menu`, `navlist`,
`navbar`, `sidebar`, `header`, `pagination`, `tooltip`, `icon` (Heroicons,
nombres kebab-case: `plus`, `pencil`, `trash`, `eye`, `magnifying-glass`,
`check-circle`, `x-mark`, `cube`, `archive-box`, `tag`, `home`,
`arrow-trending-up`, `shopping-bag`, `chart-bar`). No hay componente de
subida de archivos — usa `<input type="file" wire:model="...">` a mano
(ver `ProductoForm` para el estilo).

Snippets reales copiados de Producto (modal completo, tabla con columnas
ordenables por clic, badges de estado, formulario con validación automática)
en [references/flux-patterns.md](references/flux-patterns.md).

Reglas rápidas:
- **Theming por defecto de Flux** (zinc/accent) — no reintroduzcas los colores
  MD3 (`primary`/`secondary`/`tertiary` custom) en componentes Flux. Para
  color semántico usa las props nativas: `<flux:badge color="green">`,
  `<flux:callout variant="success">`, `<flux:button variant="danger">`.
- **Validación automática**: `<flux:input wire:model="campo" label="...">`
  muestra el mensaje de error solo si existe una regla de validación para
  `campo` en el componente Livewire — no agregues bloques `@error` manuales
  como en el sistema legado.
- **Modal**: `<flux:modal wire:model="mostrarModal">` — Flux entabla el
  `wire:model` automáticamente (`$attributes->wire('model')` +
  `@entangle` internos), no necesitas Alpine manual ni un componente modal
  custom. Existió un `<x-modal>` hecho a mano en un borrador anterior de este
  skill — se eliminó, no lo repongas.
- **Tabla con paginación**: `<flux:table :paginate="$items">` renderiza la
  paginación solo — no llames `{{ $items->links() }}` aparte.
- **Sort por columna**: usa `sortable :sorted="..." :direction="..." wire:click="sort('columna')"`
  en `flux:table.column` en vez de un `<select>` de "ordenar por" — ver
  `ProductoTable`/`producto-table.blade.php` para el método `sort()` completo.

## Interactividad: siempre Livewire para módulos nuevos

A diferencia del sistema legado (que evitaba Livewire fuera de auth/settings),
**todo módulo nuevo o migrado usa Livewire real** — filtros con
`wire:model.live.debounce`, `wire:click` para acciones de fila, `wire:confirm`
para confirmaciones de borrado (reemplaza el `onsubmit="return confirm()"` de
JS plano del sistema legado). No repliques el patrón de controlador +
`@push('scripts')` en código nuevo.

## Verificar visualmente

Después de tocar una vista, arranca el servidor y mírala en el navegador real
— usa el skill [run-tiendax](../run-tiendax/SKILL.md) para levantar el server
y el usuario de prueba, y el Browser tool para navegar y verificar. Ojo: al
probar con clics automatizados en el Browser tool, un clic ocasionalmente no
llega al elemento real tras un morph de Livewire (refs desactualizadas) —
si algo no reacciona, vuelve a leer la página (`read_page`) antes de reintentar
el clic, o verifica el estado real vía `window.Livewire.all()` /
`$wire.propiedad` con `javascript_tool` antes de asumir que el componente está
roto.
