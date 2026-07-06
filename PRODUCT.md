# Product

## Register

brand

## Users

Dois públicos, um só dono de marca (Samir Hanna Verza — dev/sysadmin brasileiro):

- **Visitantes técnicos** (devs, sysadmins, entusiastas Linux) que chegam por link, GitHub ou indicação para baixar uma ferramenta específica ou ver o que o Samir construiu. Contexto: desktop ou mobile, avaliando em segundos se vale instalar. Job: entender o que é, confiar na procedência e baixar/usar a última versão sem atrito.
- **O próprio Samir**, como vitrine pessoal — a página comunica o nível de craft dele. É portfólio disfarçado de central de downloads.

A superfície pública é **brand** (a impressão é o produto). O `/admin` é *product* e fica fora deste register.

## Product Purpose

Central pessoal para organizar e distribuir os projetos do Samir — apps desktop, ferramentas CLI/Rust, utilitários e produtos como **ShvIA** e **SShvTerm** — com download contado/auditado e sempre a versão mais recente. Sucesso = o visitante entende o projeto em segundos, confia e baixa/abre sem fricção; e a página deixa claro que por trás há engenharia séria.

## Brand Personality

Preciso, engenheiro, confiante-sem-alarde. Voz técnica e direta, em pt-BR, sem marketing vazio. Três palavras (objeto físico): **usinado, calmo, exato** — a sensação de um instrumento bem-feito. Não um SaaS genérico; não um "terminal hacker" de fantasia.

## Anti-references

- **Costume de terminal**: marquee de ícones de tecnologia, cursor piscando, comentários `//` como eyebrow em toda seção, monospace como decoração em texto que não é código. (Era o visual anterior — estamos saindo dele.)
- **SaaS-template genérico**: grades de cards idênticos (ícone + título + texto), hero-metric (número gigante + label), gradient-text, glassmorphism decorativo.
- **Editorial-magazine cliché**: serif display em itálico + drop caps + grid de jornal. "Sóbrio" aqui é *restraint técnico*, não revista.

## Design Principles

1. **Autenticidade sobre performance** — um dev de verdade mostrando trabalho de verdade; nada que ele mesmo não usaria. Mono só onde é código.
2. **A procedência é a feature** — versão, data, SO, arquitetura, hash e contagem de downloads mostrados com clareza geram a confiança que faz a pessoa instalar.
3. **Sóbrio, não tímido** — restraint com um ou dois momentos de convicção (o acento indigo usado com intenção; a tipografia com presença). Seguro demais = invisível.
4. **Menos atrito no download** — o caminho "entendi → baixei o certo pro meu SO" é o fluxo sagrado; product-sense nas páginas funcionais.
5. **Mostre o que o código faz** — imagery = screenshots reais e painéis de produto de alto craft, não blocos de cor.

## Accessibility & Inclusion

Dark-only (decisão de marca). Contraste AA obrigatório: corpo ≥ 4.5:1 (acento em texto pequeno usa o tom claro `#818cf8`/`#a5b4fc`, nunca `#6366f1`). `prefers-reduced-motion` respeitado em toda animação. Navegação por teclado com foco visível; abas de SO com ARIA. pt-BR como idioma principal.
