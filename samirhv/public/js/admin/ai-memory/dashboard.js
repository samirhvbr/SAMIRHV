/**
 * AI-MEMORY · Dashboard — leitura dos gráficos + atualização ao vivo.
 *
 * Os gráficos já vêm desenhados do servidor (funcionam sem JS); este arquivo
 * acrescenta a leitura fina (crosshair + tooltip, teclado, tabela equivalente,
 * troca de métrica) e o "ao vivo": um polling curto em /admin/ai-memory/live
 * que troca números e barras no lugar, sem recarregar a página. Nada aqui é a
 * ÚNICA forma de ler um valor — o botão "tabela" e os rótulos cobrem tudo.
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

    /** Teto "redondo" do eixo Y — espelha DashboardSummary::niceMax() no PHP. */
    const niceMax = (max) => {
        if (max <= 5) return Math.max(max, 1);
        const step = Math.max(1, 10 ** Math.floor(Math.log10(max)) / 2);
        return Math.ceil(max / step) * step;
    };

    /** Posiciona o tooltip ao lado do ponto, virando para dentro do card. */
    const placeTip = (tip, x, y, width) => {
        const left = x + 14 + tip.offsetWidth > width ? x - tip.offsetWidth - 14 : x + 14;
        tip.style.left = Math.max(0, left) + 'px';
        tip.style.top = Math.max(0, y) + 'px';
    };

    // Preenchido pelo bloco da Atividade; usado pelo "ao vivo" para redesenhar.
    let refreshSeries = null;

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

        /**
         * Redesenha as duas faixas com uma série nova (mesmos dias) — barras,
         * teto do eixo, números da legenda e a tabela equivalente. Só mexe no que
         * mudou de valor: nenhum nó é recriado, então nada pisca nem pula.
         */
        refreshSeries = (nextDays, nextObs, nextSes) => {
            const series = [
                { key: 'obs', values: nextObs, cols: cols[0], store: obs },
                { key: 'ses', values: nextSes, cols: cols[1], store: ses },
            ];

            days.length = 0;
            days.push(...nextDays);

            series.forEach(({ key, values, cols: list, store }) => {
                if (!list || !values.length) return;
                store.length = 0;
                store.push(...values);

                const max = Math.max(...values, 0);
                const top = niceMax(max);
                const totals = values.reduce((a, b) => a + b, 0);

                list.forEach((col, i) => {
                    const bar = col.querySelector('.aim-colbar');
                    if (!bar) return;
                    const value = values[i] ?? 0;
                    bar.classList.toggle('aim-colbar--zero', value === 0);
                    bar.classList.toggle('aim-colbar--peak', value > 0 && value === max);
                    bar.style.height = value > 0 ? (value / top) * 100 + '%' : '';
                });

                const peakIndex = values.lastIndexOf(max);
                const set = (name, text) => {
                    const el = document.querySelector(`[data-live="${key}.${name}"]`);
                    if (el && el.textContent !== text) el.textContent = text;
                };
                set('top', fmt.format(top));
                set('total', fmt.format(totals));
                set('avg', fmt.format(Math.round(totals / Math.max(values.length, 1))));
                set('max', fmt.format(max));
                if (max > 0 && nextDays[peakIndex]) set('peak', nextDays[peakIndex]);
            });

            // tabela equivalente (mais recente primeiro), célula a célula
            const rowsEl = table ? [...table.querySelectorAll('tbody tr')] : [];
            rowsEl.forEach((tr, i) => {
                const idx = nextDays.length - 1 - i;
                if (idx < 0) return;
                const cells = tr.cells;
                const values = [nextDays[idx], fmt.format(nextObs[idx] ?? 0), fmt.format(nextSes[idx] ?? 0)];
                values.forEach((text, c) => {
                    if (cells[c] && cells[c].textContent !== text) cells[c].textContent = text;
                });
            });

            if (current >= 0 && plot.classList.contains('is-hovering')) show(current);
        };
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

    // ── "Ao vivo": polling curto, pausável, que troca os números no lugar ────
    const live = document.querySelector('[data-aim-live]');
    if (live) {
        const url = live.dataset.url;
        const every = Math.max(5, Number(live.dataset.every) || 15) * 1000;
        const btn = live.querySelector('[data-aim-livetoggle]');
        const label = live.querySelector('[data-aim-livelabel]');
        const when = live.querySelector('[data-aim-livewhen]');
        const root = document.querySelector('.aim');
        const KEY = 'aim.live.paused';

        let paused = localStorage.getItem(KEY) === '1';
        let lastAt = null;
        let failures = 0;
        let timer = null;
        let inFlight = null;

        const setState = (state) => live.setAttribute('data-state', state);

        /** "agora mesmo" / "há 12s" / "há 3min" — sem depender de biblioteca. */
        const ago = () => {
            if (!lastAt) return '';
            const s = Math.round((Date.now() - lastAt) / 1000);
            if (s < 5) return 'agora mesmo';
            if (s < 60) return `há ${s}s`;
            const m = Math.round(s / 60);
            return m < 60 ? `há ${m}min` : `há ${Math.round(m / 60)}h`;
        };

        const paint = () => {
            if (paused) {
                when.textContent = lastAt ? `pausado · ${ago()}` : 'pausado';
            } else if (failures > 0) {
                when.textContent = 'sem resposta do servidor';
            } else {
                when.textContent = ago();
            }
        };

        const apply = (data) => {
            if (!data || data.available === false) {
                setState('error');
                return;
            }

            // totais
            Object.entries(data.counts || {}).forEach(([key, value]) => {
                const el = document.querySelector(`[data-live="counts.${key}"]`);
                const text = fmt.format(value);
                if (el && el.textContent !== text) el.textContent = text;
            });

            // variação vs. o retrato de referência (que não muda no meio do dia)
            document.querySelectorAll('[data-live-delta]').forEach((el) => {
                const key = el.dataset.liveDelta;
                const ref = Number(el.dataset.ref);
                const now = Number((data.counts || {})[key]);
                if (!Number.isFinite(ref) || !Number.isFinite(now)) return;
                const diff = now - ref;
                const value = el.querySelector('b');
                if (value) value.textContent = (diff > 0 ? '+' : '') + fmt.format(diff);
                const arrow = el.querySelector('i');
                if (arrow) arrow.textContent = diff > 0 ? '▲' : diff < 0 ? '▼' : '•';
                el.classList.toggle('aim-delta--up', diff > 0);
                el.classList.toggle('aim-delta--down', diff < 0);
                el.classList.toggle('aim-delta--flat', diff === 0);
            });

            const obsByDay = data.observationsByDay || {};
            const sesByDay = data.sessionsByDay || {};
            const keys = Object.keys(obsByDay);
            if (keys.length) {
                const today = document.querySelector('[data-live="obs.today"]');
                if (today) today.textContent = fmt.format(obsByDay[keys[keys.length - 1]] ?? 0);

                if (refreshSeries) {
                    // dd/mm, o mesmo formato do eixo desenhado pelo servidor
                    const labels = keys.map((iso) => iso.slice(8, 10) + '/' + iso.slice(5, 7));
                    refreshSeries(labels, Object.values(obsByDay), keys.map((k) => sesByDay[k] ?? 0));
                }
            }

            lastAt = Date.now();
            failures = 0;
            setState('live');
        };

        const tick = async () => {
            if (paused || document.hidden || inFlight) return;
            root?.classList.add('aim-refetching');
            try {
                inFlight = fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                const response = await inFlight;
                if (!response.ok) throw new Error(String(response.status));
                apply(await response.json());
            } catch {
                failures += 1;
                setState('error');
            } finally {
                inFlight = null;
                root?.classList.remove('aim-refetching');
                paint();
            }
        };

        const schedule = () => {
            clearTimeout(timer);
            if (paused) return;
            // erro seguido espaça as tentativas (até 2 min) em vez de martelar
            const delay = failures > 0 ? Math.min(every * 2 ** failures, 120000) : every;
            timer = setTimeout(async () => {
                await tick();
                schedule();
            }, delay);
        };

        const setPaused = (value) => {
            paused = value;
            localStorage.setItem(KEY, value ? '1' : '0');
            btn.setAttribute('aria-pressed', String(!value));
            label.textContent = value ? 'pausado' : 'ao vivo';
            setState(value ? 'paused' : 'live');
            paint();
            schedule();
            if (!value) tick();
        };

        btn.addEventListener('click', () => setPaused(!paused));

        // aba escondida não consome requisição; ao voltar, atualiza na hora
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && !paused) tick();
        });

        setInterval(paint, 5000);   // só o rótulo "há Xs"
        setPaused(paused);
    }
})();
