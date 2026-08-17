<style>
    /* ── Footer ── */
    .site-footer {
        background: var(--white, #ffffff);
        border: 1px solid var(--slate-200, #e2e8f0);
        border-radius: var(--radius-md, 8px);
        box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1));
        margin-top: 32px;
        padding: 24px;
        color: var(--slate-600, #475569);
    }

    .footer-container {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 32px;
    }

    .footer-brand {
        flex: 1;
        min-width: 220px;
    }

    .footer-brand h2 {
        margin: 0 0 6px;
        font-size: 17px;
        font-weight: 700;
        color: var(--slate-900, #0f172a);
        letter-spacing: -.3px;
    }

    .footer-brand p {
        margin: 0;
        max-width: 360px;
        font-size: 12px;
        line-height: 1.6;
        color: var(--slate-500, #64748b);
    }

    .footer-section {
        min-width: 150px;
    }

    .footer-section h3 {
        margin: 0 0 10px;
        font-size: 12px;
        font-weight: 700;
        color: var(--slate-800, #1e293b);
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .footer-details {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .footer-detail {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        color: var(--slate-500, #64748b);
    }

    .footer-detail svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        color: var(--slate-400, #94a3b8);
    }

    .footer-detail a {
        color: inherit;
        text-decoration: none;
    }

    .footer-detail a:hover {
        color: var(--slate-700, #334155);
    }

    .footer-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 22px;
        padding-top: 14px;
        border-top: 1px solid var(--slate-200, #e2e8f0);
        font-size: 11px;
        color: var(--slate-400, #94a3b8);
    }

    /* ── Mobile Footer ── */
    @media (max-width: 768px) {

        .site-footer {
            margin-top: 20px;
            padding: 18px 14px;
            border-radius: 8px;
        }

        .footer-container {
            flex-direction: column;
            gap: 20px;
        }

        .footer-brand {
            min-width: 0;
        }

        .footer-brand h2 {
            font-size: 16px;
        }

        .footer-brand p {
            max-width: none;
            font-size: 11px;
        }

        .footer-section {
            width: 100%;
            min-width: 0;
        }

        .footer-section h3 {
            margin-bottom: 8px;
        }

        .footer-bottom {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
            margin-top: 18px;
            padding-top: 12px;
        }
    }
</style>

<!-- Footer -->
<footer class="site-footer">

    <div class="footer-container">

        <!-- Company -->
        <div class="footer-brand">
            <h2>Nexora Technologies</h2>

            <p>
                Nexora Technologies provides secure and efficient
                technology solutions for modern businesses, with a
                focus on reliable asset and resource management.
            </p>
        </div>

        <!-- Company Details -->
        <div class="footer-section">

            <h3>Contact</h3>

            <div class="footer-details">

                <div class="footer-detail">
                    <svg viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>

                    <span>
                        402, Orion Business Park, Surat, Gujarat
                    </span>
                </div>

                <div class="footer-detail">
                    <svg viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                                 19.79 19.79 0 0 1-8.63-3.07
                                 19.5 19.5 0 0 1-6-6
                                 19.79 19.79 0 0 1-3.07-8.67
                                 A2 2 0 0 1 4.11 2h3
                                 a2 2 0 0 1 2 1.72
                                 12.84 12.84 0 0 0 .7 2.81
                                 2 2 0 0 1-.45 2.11L8.09 9.91
                                 a16 16 0 0 0 6 6l1.27-1.27
                                 a2 2 0 0 1 2.11-.45
                                 12.84 12.84 0 0 0 2.81.7
                                 A2 2 0 0 1 22 16.92Z" />
                    </svg>

                    <a href="tel:+912612345678">
                        +91 261 234 5678
                    </a>
                </div>

                <div class="footer-detail">
                    <svg viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect width="20" height="16"
                            x="2" y="4" rx="2" />
                        <path d="m22 7-8.97 5.7a1.94 1.94
                                 0 0 1-2.06 0L2 7" />
                    </svg>

                    <a href="mailto:hello@nexoratech.example">
                        hello@nexoratech.example
                    </a>
                </div>

            </div>

        </div>

        <!-- System -->
        <div class="footer-section">

            <h3>Platform</h3>

            <div class="footer-details">

                <div class="footer-detail">
                    <span>Asset Management</span>
                </div>

                <div class="footer-detail">
                    <span>Employee Management</span>
                </div>

                <div class="footer-detail">
                    <span>Asset Requests</span>
                </div>

                <div class="footer-detail">
                    <span>System Administration</span>
                </div>

            </div>

        </div>

    </div>

    <div class="footer-bottom">

        <span>
            © <?= date('Y') ?> Nexora Technologies Pvt. Ltd.
        </span>

        <span>
            AssetTracker · v1.0
        </span>

    </div>

</footer>