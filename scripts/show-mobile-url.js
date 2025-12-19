import os from 'os';

// Get local IP address
function getLocalIP() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            // Skip internal (loopback) and non-IPv4 addresses
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }
    return null;
}

const localIP = getLocalIP();
const vitePort = 5173;
const appPort = 80;

console.log('\n' + '='.repeat(60));
console.log('📱 ACCÈS MOBILE - URLs de test');
console.log('='.repeat(60) + '\n');

if (localIP) {
    console.log('🌐 Adresse IP locale: ' + localIP);
    console.log('\n📲 Accédez à votre application depuis votre mobile:\n');
    console.log('   → Application: http://' + localIP);
    console.log('   → Vite HMR:    http://' + localIP + ':' + vitePort);
    console.log('\n💡 Assurez-vous que:');
    console.log('   1. Votre mobile est sur le MÊME réseau WiFi');
    console.log('   2. Docker est lancé (docker compose up -d)');
    console.log('   3. Vite tourne (npm run mobile ou yarn mobile)');
    console.log('   4. Le firewall autorise les connexions sur le port ' + vitePort);
} else {
    console.log('⚠️  Impossible de détecter l\'adresse IP locale');
    console.log('   Vérifiez votre connexion réseau');
}

console.log('\n' + '='.repeat(60) + '\n');
