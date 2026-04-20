/**
 * LOCKINGSTYLE - Hardware Optimization Protocol
 */

function initiatePowerGuard() {
    if ('getBattery' in navigator) {
        navigator.getBattery().then(function(battery) {
            function updatePowerState() {
                const isLow = battery.level < 0.20 && !battery.charging;
                if (isLow) {
                    document.documentElement.classList.add('low-power-mode');
                    console.warn('[POWER_GUARD] Low energy detected. Suspending UI animations.');
                } else {
                    document.documentElement.classList.remove('low-power-mode');
                }
            }

            updatePowerState();
            battery.addEventListener('levelchange', updatePowerState);
            battery.addEventListener('chargingchange', updatePowerState);
        });
    }
}

document.addEventListener('DOMContentLoaded', initiatePowerGuard);
