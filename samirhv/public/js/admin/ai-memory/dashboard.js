/**
 * AI-MEMORY · Dashboard — camada de leitura dos gráficos.
 *
 * Os gráficos já vêm desenhados do servidor (funcionam sem JS); este arquivo
 * só acrescenta a leitura fina: crosshair + tooltip, navegação por teclado,
 * tabela equivalente e troca de métrica no histórico. Nada aqui é a ÚNICA
 * forma de ler um valor — o botão "tabela" e os rótulos cobrem tudo.
 */
(() => {
    'use strict';

    const fmt = new Intl.NumberFormat('pt-BR');
    const parse = (el, attr, fallback) => {
        try {
            return JSON.parse(el.dataset[attr]);
        } catch {
            return fallback;
        }
    };

    /** Linha do tooltip: chave colorida + nome + valor (nomes via textContent). */
    const tipRow = (keyClass, name, value) => {
        const row = document.createElement('p');
        row.className = 'aim-tip__row';
        const key = document.createElement('span');
        key.className = 'aim-key ' + keyClass;
        const label = document.createElement('span');
        label.textContent = name;
        const val = document.createElement('b');
        val.textContent = fmt.format(value);
        row.append(key, label, val);
        return row;
    };

    const tipHead = (text) => {
        const head = document.createElement('p');
        head.className = 'aim-tip__day';
        head.textContent = text;
        return head;
    };

    /** Posiciona o tooltip ao lado do ponto, virando para dentro do card. */
    const placeTip = (tip, x, y, width) => {
        const left = x + 14 + tip.offsetWidth > width ? x - tip.offsetWidth - 14 : x + 14;
        tip.style.left = Math.max(0, left) + 'px';
        tip.style.top = Math.max(0, y) + 'px';
    };

    // ── Atividade: duas faixas, um eixo, um tooltip ──────────────────────────
    const plot = document.querySelector('[data-aim-plot]');
    if (plot) {
        const days = parse(plot, 'days', []);
        const obs = parse(plot, 'obs', []);
        const ses = parse(plot, 'ses', []);
        const rows = [...plot.querySelectorAll('.aim-row')];
        const cols = rows.map((row) => [...row.querySelectorAll('.aim-col')]);
        const cross = plot.querySelector('[data-aim-cross]');
        const tip = plot.querySelector('[data-aim-tip]');
        let current = -1;

        const show = (i) => {
            if (!days.length) return;
            const idx = Math.min(Math.max(i, 0), days.length - 1);
            current = idx;
            plot.classList.add('is-hovering');
            cols.forEach((list) => list.forEach((col, j) => col.classList.toggle('is-active', j === idx)));

            const box = plot.getBoundingClientRect();
            const ref = cols[0][idx].getBoundingClientRect();
            const x = ref.left - box.left + ref.width / 2;
            cross.style.left = x + 'px';
            cross.style.height = rows[rows.length - 1].getBoundingClientRect().bottom - box.top + 'px';

            tip.textContent = '';
            tip.append(
                tipHead(days[idx]),
                tipRow('aim-key--obs', 'Observações', obs[idx] ?? 0),
                tipRow('aim-key--ses', 'Sessões', ses[idx] ?? 0),
            );
            tip.classList.add('is-on');
            placeTip(tip, x, 8, box.width);
        };

        const hide = () => {
            plot.classList.remove('is-hovering');
            tip.classList.remove('is-on');
            cols.forEach((list) => list.forEach((col) => col.classList.remove('is-active')));
        };

        plot.addEventListener('pointermove', (event) => {
            const box = rows[0].getBoundingClientRect();
            const band = box.width / days.length;
            show(Math.floor((event.clientX - box.left) / band));
        });
        plot.addEventListener('pointerleave', hide);
        plot.addEventListener('blur', hide);
        plot.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
                event.preventDefault();
                show(current < 0 ? days.length - 1 : current + (event.key === 'ArrowRight' ? 1 : -1));
            } else if (event.key === 'Home') {
                event.preventDefault();
                show(0);
            } else if (event.key === 'End') {
                event.preventDefault();
                show(days.length - 1);
            } else if (event.key === 'Escape') {
                hide();
            }
        });

        const toggle = document.querySelector('[data-aim-table]');
        const table = document.querySelector('[data-aim-tablewrap]');
        if (toggle && table) {
            toggle.addEventListener('click', () => {
                const opening = table.hidden;
                table.hidden = !opening;
                toggle.setAttribute('aria-pressed', String(opening));
                toggle.setAttribute('aria-expanded', String(opening));
            });
        }
    }

    // ── Histórico: troca de métrica + leitura por data ───────────────────────
    const area = document.querySelector('[data-aim-area]');
    if (area) {
        const dates = parse(area, 'dates', []);
        const series = parse(area, 'series', {});
        const labels = parse(area, 'labels', {});
        const areaPath = area.querySelector('[data-aim-areapath]');
        const linePath = area.querySelector('[data-aim-linepath]');
        const dot = area.querySelector('[data-aim-areadot]');
        const end = area.querySelector('[data-aim-areaend]');
        const yTop = area.querySelector('[data-aim-ytop]');
        const yMid = area.querySelector('[data-aim-ymid]');
        const hit = area.querySelector('[data-aim-areahit]');
        const tip = area.querySelector('[data-aim-areatip]');
        const buttons = [...area.closest('section').querySelectorAll('[data-aim-metric]')];
        const W = 1000;
        const H = 220;
        let metric = 'observations';
        let top = 1;
        let current = -1;

        // mesmo teto "redondo" do PHP (DashboardSummary::niceMax)
        const niceMax = (max) => {
            if (max <= 5) return Math.max(max, 1);
            const step = Math.max(1, 10 ** Math.floor(Math.log10(max)) / 2);
            return Math.ceil(max / step) * step;
        };

        const render = () => {
            const values = series[metric] || [];
            if (!values.length) return;
            top = niceMax(Math.max(...values, 1));
            const step = W / Math.max(values.length - 1, 1);
            const points = values.map((v, i) => `${(i * step).toFixed(2)},${(H - (v / top) * H).toFixed(2)}`);
            const line = 'M' + points.join(' L');

            linePath.setAttribute('d', line);
            areaPath.setAttribute('d', `${line} L${W},${H} L0,${H} Z`);

            const last = values[values.length - 1];
            const y = (1 - last / top) * 100 + '%';
            dot.style.top = y;
            end.style.top = y;
            end.textContent = fmt.format(last);
            yTop.textContent = fmt.format(top);
            yMid.textContent = fmt.format(Math.round(top / 2));
            hit.setAttribute(
                'aria-label',
                `Evolução histórica: ${labels[metric]}, de ${dates[0]} a ${dates[dates.length - 1]} — ` +
                `de ${fmt.format(values[0])} a ${fmt.format(last)}.`,
            );
        };

        const read = (index) => {
            const values = series[metric] || [];
            if (!values.length) return;
            const idx = Math.min(Math.max(index, 0), values.length - 1);
            current = idx;
            tip.textContent = '';
            tip.append(tipHead(dates[idx]), tipRow('aim-key--obs', labels[metric], values[idx]));
            tip.classList.add('is-on');
            const box = hit.getBoundingClientRect();
            const x = (idx / Math.max(values.length - 1, 1)) * box.width;
            placeTip(tip, x, Math.max(0, (1 - values[idx] / top) * box.height - 12), box.width);
        };

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                metric = button.dataset.aimMetric;
                buttons.forEach((other) => other.setAttribute('aria-pressed', String(other === button)));
                render();
                tip.classList.remove('is-on');
                current = -1;
            });
        });

        hit.addEventListener('pointermove', (event) => {
            const box = hit.getBoundingClientRect();
            const values = series[metric] || [];
            read(Math.round(((event.clientX - box.left) / box.width) * (values.length - 1)));
        });
        hit.addEventListener('pointerleave', () => tip.classList.remove('is-on'));
        hit.addEventListener('blur', () => tip.classList.remove('is-on'));
        hit.addEventListener('keydown', (event) => {
            const values = series[metric] || [];
            if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
                event.preventDefault();
                read(current < 0 ? values.length - 1 : current + (event.key === 'ArrowRight' ? 1 : -1));
            } else if (event.key === 'Home') {
                event.preventDefault();
                read(0);
            } else if (event.key === 'End') {
                event.preventDefault();
                read(values.length - 1);
            } else if (event.key === 'Escape') {
                tip.classList.remove('is-on');
                current = -1;
            }
        });

        render();
    }
})();
