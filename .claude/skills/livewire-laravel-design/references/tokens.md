# Tokens MD3 — tiendax (sistema legado)

> **Legado.** Esto describe el sistema Tailwind MD3 hecho a mano que todavía
> usan las páginas no migradas (dashboard, categorías, clientes, ventas,
> movimientos, reportes). Para módulos nuevos o en migración usa Flux UI con
> su theming por defecto — ver el [SKILL.md](../SKILL.md) principal y
> [flux-patterns.md](flux-patterns.md). No introduzcas estos tokens en
> componentes `<flux:*>`.

Fuente: `tailwind.config` inline en `resources/views/components/layouts/app.blade.php`
y duplicado en `resources/views/components/layouts/sales.blade.php`. Si agregas
o cambias un token, hazlo en ambos archivos.

## Color

| Token | Hex | Uso |
|---|---|---|
| `primary` | `#24389c` | Acción principal, links activos, sidebar activo |
| `on-primary` | `#ffffff` | Texto/icono sobre `primary` |
| `primary-container` | `#3f51b5` | Variante más clara de acento |
| `on-primary-container` | `#cacfff` | Texto sobre `primary-container` |
| `primary-fixed` / `primary-fixed-dim` | `#dee0ff` / `#bac3ff` | Gradientes de tarjetas bento |
| `on-primary-fixed` / `on-primary-fixed-variant` | `#00105c` / `#293ca0` | — |
| `inverse-primary` | `#bac3ff` | — |
| `secondary` | `#b80049` | Acción destacada/alerta suave — "Nuevo Producto", notificación, bajo stock |
| `on-secondary` | `#ffffff` | Texto sobre `secondary` |
| `secondary-container` | `#e2165f` | — |
| `on-secondary-container` | `#fffbff` | — |
| `secondary-fixed` / `secondary-fixed-dim` | `#ffd9de` / `#ffb2be` | — |
| `on-secondary-fixed` / `on-secondary-fixed-variant` | `#400014` / `#900038` | — |
| `tertiary` | `#004a55` | Valores positivos/monetarios — ingresos, margen alto, "en stock" |
| `on-tertiary` | `#ffffff` | Texto sobre `tertiary` |
| `tertiary-container` | `#006471` | — |
| `on-tertiary-container` | `#55e4fd` | — |
| `tertiary-fixed` / `tertiary-fixed-dim` | `#a1efff` / `#44d8f1` | — |
| `on-tertiary-fixed` / `on-tertiary-fixed-variant` | `#001f25` / `#004e59` | — |
| `error` | `#ba1a1a` | Estados de error, "agotado", eliminar |
| `on-error` | `#ffffff` | Texto sobre `error` |
| `error-container` | `#ffdad6` | Fondo de banners de error |
| `on-error-container` | `#93000a` | Texto sobre `error-container` |
| `background` | `#f8f9fa` | Fondo del `<body>` |
| `on-background` | `#191c1d` | Texto por defecto |
| `surface` | `#f8f9fa` | Fondo del header |
| `surface-dim` / `surface-bright` | `#d9dadb` / `#f8f9fa` | — |
| `surface-container-lowest` | `#ffffff` | Fondo de tarjetas (el "blanco" de tarjetas) |
| `surface-container-low` | `#f3f4f5` | Fondo de cabecera de tabla, hover de fila |
| `surface-container` | `#edeeef` | — |
| `surface-container-high` | `#e7e8e9` | Fondo de botón "cancelar", hover de nav |
| `surface-container-highest` | `#e1e3e4` | Fondo del sidebar |
| `surface-variant` | `#e1e3e4` | — |
| `on-surface` | `#191c1d` | Texto principal sobre superficies |
| `on-surface-variant` | `#454652` | Texto secundario, labels |
| `inverse-surface` / `inverse-on-surface` | `#2e3132` / `#f0f1f2` | Tooltips oscuros (hints de teclado en POS) |
| `outline` | `#757684` | Bordes de icono, texto terciario ("—", placeholders) |
| `outline-variant` | `#c5c5d4` | Bordes de tarjetas/inputs/tablas |
| `surface-tint` | `#4355b9` | — |

Convención de opacidad: para fondos suaves de badges/iconos usa el color base
con `/10` o `/20` (`bg-primary/10`, `bg-error-container/20`), no una variante
de color distinta.

## Border radius

| Token | Valor |
|---|---|
| `DEFAULT` | `0.25rem` |
| `lg` | `0.5rem` |
| `xl` | `0.75rem` |
| `full` | `9999px` |

## Espaciado custom

| Token | Valor | Uso |
|---|---|---|
| `sidebar-width` | `260px` | Ancho fijo del sidebar (`w-[260px]`, `ml-[260px]`) |
| `gutter` | `1.5rem` | Padding horizontal de header/sidebar (`px-gutter`) |
| `container-padding` | `2rem` | Padding del `<main>` (`px-container-padding`) |
| `stack-lg/md/sm` | `2rem` / `1rem` / `0.5rem` | Espaciados verticales genéricos |

## Tipografía

Cada token de tamaño trae su propio `fontFamily` asociado — úsalos siempre en
pareja `font-{token} text-{token}`, nunca combines un tamaño de Tailwind
estándar (`text-2xl`) con estos tokens.

| Token | Tamaño | Line-height | Otros | Fuente |
|---|---|---|---|---|
| `headline-xl` | 40px | 48px | letter-spacing -0.02em, weight 700 | Hanken Grotesk |
| `headline-lg` | 32px | 40px | letter-spacing -0.01em, weight 600 | Hanken Grotesk |
| `headline-md` | 24px | 32px | weight 600 | Hanken Grotesk |
| `body-lg` | 18px | 28px | weight 400 | Inter |
| `body-md` | 16px | 24px | weight 400 | Inter |
| `body-sm` | 14px | 20px | weight 400 | Inter |
| `label-lg` | 14px | 20px | letter-spacing 0.02em, weight 600 | Inter |
| `label-sm` | 12px | 16px | weight 500 | Inter |
| `mono-data` | 14px | 20px | weight 500 | Inter |

`headline-*` para títulos de página/sección. `body-*` para texto de párrafo.
`label-*` para labels de formulario, texto de botón, texto de tabla/badges.
`mono-data` para SKUs y datos tabulares que deban alinearse.
