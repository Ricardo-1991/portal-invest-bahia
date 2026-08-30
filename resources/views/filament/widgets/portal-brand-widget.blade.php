<x-filament-widgets::widget class="pib-brand-widget">
    <section class="pib-brand-widget__surface" aria-labelledby="pib-brand-widget-title">
        <div class="pib-brand-widget__glow" aria-hidden="true"></div>

        <img
            src="{{ asset('images/logo.jpeg') }}"
            alt="Logo do Portal Invest Bahia"
            class="pib-brand-widget__logo"
        >

        <div class="pib-brand-widget__copy">
            <p class="pib-brand-widget__eyebrow">Painel administrativo</p>
            <h2 id="pib-brand-widget-title">Portal Invest Bahia</h2>
            <p>Gestão de classificados, eventos e conteúdo institucional.</p>
        </div>
    </section>

    <style>
        .pib-brand-widget__surface {
            position: relative;
            isolation: isolate;
            min-height: 9.75rem;
            overflow: hidden;
            display: grid;
            grid-template-columns: 7.25rem 1fr;
            align-items: center;
            gap: 1.25rem;
            padding: 1rem 1.5rem 1rem 1rem;
            border: 1px solid rgb(213 157 22 / 28%);
            border-radius: .75rem;
            background: #0b0b0a;
            box-shadow: 0 1px 3px rgb(55 40 5 / 12%);
            color: #f7f3e8;
        }

        .pib-brand-widget__glow {
            position: absolute;
            z-index: -1;
            top: -8rem;
            right: -5rem;
            width: 18rem;
            height: 18rem;
            border-radius: 50%;
            background: radial-gradient(circle, rgb(213 157 22 / 20%), transparent 68%);
        }

        .pib-brand-widget__logo {
            width: 7.25rem;
            height: 7.25rem;
            border-radius: .5rem;
            object-fit: cover;
            box-shadow: 0 10px 28px rgb(0 0 0 / 32%);
        }

        .pib-brand-widget__copy {
            min-width: 0;
        }

        .pib-brand-widget__eyebrow {
            margin-bottom: .35rem;
            color: #d5a52d;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .pib-brand-widget__copy h2 {
            color: #fffaf0;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: -.02em;
            line-height: 1.3;
        }

        .pib-brand-widget__copy > p:last-child {
            max-width: 34ch;
            margin-top: .35rem;
            color: #c9c3b5;
            font-size: .85rem;
            line-height: 1.5;
            text-wrap: pretty;
        }

        @media (max-width: 420px) {
            .pib-brand-widget__surface {
                grid-template-columns: 5rem 1fr;
                gap: .9rem;
                padding: .85rem;
            }

            .pib-brand-widget__logo {
                width: 5rem;
                height: 5rem;
            }
        }
    </style>
</x-filament-widgets::widget>
