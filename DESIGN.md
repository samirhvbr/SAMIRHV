# Design

Sistema visual da **vitrine pública** do Samirhv. Dark-only, *restraint* técnico com um acento indigo. Mira: a sobriedade dark de Linear/Vercel/Resend — mas com voz própria (tipografia Archivo, acento indigo, imagery de produto real). Evita: costume de terminal, SaaS-template, editorial-magazine. Ver [PRODUCT.md](PRODUCT.md).

## Theme

Dark-only. Canvas quase-preto com leve tinta índigo; profundidade por elevação sutil (surfaces + hairlines), nunca por sombra pesada ou glass decorativo.

## Color

Shipado como hex/rgb (compat. tema Canvas), pensado em OKLCH. Tokens `--s-*`:

| Token | Valor | Uso |
|---|---|---|
| `--s-bg` | `#0b0b11` | canvas |
| `--s-bg-2` | `#101019` | seção alternada |
| `--s-surface` | `#14141e` | superfície/cartão |
| `--s-surface-2` | `#1b1b28` | superfície elevada |
| `--s-line` | `rgba(150,155,185,.12)` | hairline neutro |
| `--s-line-2` | `rgba(99,102,241,.22)` | hairline acento |
| `--s-ink` | `#f4f5fb` | texto primário (~15:1) |
| `--s-ink-2` | `#c3c8d8` | texto secundário (~9:1) |
| `--s-muted` | `#8b93a7` | meta/legenda — **piso do corpo** (~5.4:1) |
| `--s-faint` | `#626a7e` | só decorativo/large, nunca corpo |
| `--s-accent` | `#6366f1` | indigo core: fills, foco, ícone |
| `--s-accent-ink` | `#818cf8` / `#a5b4fc` | acento como **texto/link** em dark |
| `--s-accent-deep` | `#4f46e5` | pressed/hover fill |
| `--s-ok` `--s-warn` `--s-danger` | `#34d399` `#f5b301` `#f87171` | semânticos |

**Regra dura:** `#6366f1` nunca como texto pequeno (contraste ~4.7). Link/label de acento = `#818cf8`/`#a5b4fc`. Estratégia: *Restrained* (canvas neutro + 1 acento ≤ ~12% da superfície) com momentos *Committed* pontuais (hero, destaque do agente).

## Typography

- **Archivo** (Google Fonts, variável) — família principal: display + títulos + corpo. Peso e tamanho carregam a hierarquia (não pareia dois sans).
- **Archivo** em largura *expanded* (`font-stretch:125%`) — só no display do hero (largura arquitetônica, convicção).
- **JetBrains Mono** — apenas código, comandos, versão/hash e metadados curtos. Nunca prosa.
- *Inter: aposentada na vitrine pública* (era default genérico — reflex-reject).

Escala fluida (`clamp()`, ratio ≥ 1.25):

- Display/hero: `clamp(2.6rem, 6vw, 4.6rem)`, 640, tracking −0.03em, `text-wrap:balance`
- H2 seção: `clamp(1.85rem, 3.2vw, 2.55rem)`, 640
- H3: 1.15–1.35rem, 600
- Corpo: 1–1.0625rem, 400, line-height 1.7 (dark → +0.05), medida 65–72ch
- Meta/mono: .72–.8rem, 500, tracking leve

## Components

- `.s-btn` (indigo fill) · `.s-btn--ghost` (hairline) · `.s-btn--sm`. Raio 10px, foco visível.
- `.s-card` — superfície hairline, **sem glass**; hover = borda acento + elevação ≤2px. Cards só quando são o affordance certo; nunca grade idêntica repetida sem variação.
- `.s-tag` — chip de categoria/tech: mono, hairline, baixo peso.
- `.s-kicker` — **no máximo um** por página, não em toda seção; sem `//`.
- `.s-term` — painel terminal/agente como *imagery* de produto: **estático** (sem cursor piscando), header de janela sóbrio, conteúdo real. Hero + destaque do agente.
- `.s-prose` — corpo de página de projeto, medida limitada.
- `.s-ostabs` — abas por SO com ARIA (padrão atual retematizado).

## Layout

- Containers: `--s-w:1120px` (geral), `--s-w-prose:720px` (leitura).
- Espaço de seção fluido: `clamp(4.5rem, 9vw, 8rem)` vertical; ritmo **variado** (nem toda seção igual).
- Grades sem breakpoint: `repeat(auto-fit,minmax(280px,1fr))` quando cards forem certos.
- Hero assimétrico (texto + painel de produto). Vitrine de projetos: destaque diferenciado + lista/grade leve — não cards idênticos.

## Motion

- **Removidos:** marquee, cursor piscando, border-shimmer.
- **Dot-grid do hero:** restaurado a pedido (0.4.1) — bolinhas índigo (`#6366f1`, grade 24px, opacity 0.35) driftando 12px em 8s sobre a aura; desligado em `prefers-reduced-motion`.
- **Page-load:** um reveal escalonado sutil (opacity + translateY pequeno), ease-out-expo; stagger só onde há lista, e sempre sobre conteúdo já visível por padrão.
- **Hover:** borda/opacity/translateY ≤ 2px; sem bounce/elastic.
- `prefers-reduced-motion: reduce` → tudo vira crossfade/instantâneo.

## Accessibility

AA em todo texto; foco visível; alvos ≥ 40px; ARIA nas abas; reduced-motion. Dark-only assumido.
