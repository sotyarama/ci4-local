    /* QZ Tray Trial Print Script */
    
    /* Include QZ Tray JavaScript library --> below line */
    // <script src="<?= base_url('assets/js/qz-tray/qz-tray.js') ?>"></script>
    
    /* Your custom QZ Tray trial print logic --> check with GPT first */
        async function ensureQZ() {
            if (qz.websocket.isActive()) return;
            await qz.websocket.connect(); // akan connect ke ws://localhost:8182 milikmu
        }

        async function printTest() {
            const printer = await qz.printers.getDefault(); // "58mm Series Printer"
            const config = qz.configs.create(printer);

            // ESC/POS raw text test
            const data = [
                '\x1B\x40', // init
                'TEMU RASA\n',
                'Test print OK\n',
                '------------------------------\n',
                new Date().toLocaleString() + '\n',
                '\n',
                'Terima kasih!\n',
                '\n\n\n'
            ];

            await qz.print(config, data);
        }

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                if (!window.qz) throw new Error('qz-tray.js belum ter-load');

                await ensureQZ();
                console.log('QZ Tray connected');

                await printTest();
                console.log('Print sent');
            } catch (err) {
                console.error('QZ error:', err);
            }
        });
