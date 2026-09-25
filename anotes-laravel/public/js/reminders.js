/**
 * ANoteS Reminder Notification System (Phase 7)
 * Handles client-side timing, repeat occurrence logic, in-app popups,
 * Browser Notification API alerts, and audio notifications.
 */

(function () {
    'use strict';

    // In-memory set to prevent duplicate alerts within the same session
    const alertedKeys = new Set();

    /**
     * Compute the next occurrence date for a reminder based on its repeat configuration.
     */
    function calculateNextOccurrence(reminder) {
        if (!reminder.remind_date || !reminder.remind_time) {
            return null;
        }

        // Format time ensuring HH:MM:SS
        let timeStr = reminder.remind_time;
        if (timeStr.length === 5) {
            timeStr += ':00';
        }

        let due = new Date(`${reminder.remind_date}T${timeStr}`);
        if (isNaN(due.getTime())) {
            return null;
        }

        const now = new Date();
        const repeat = reminder.repeat_option || 'none';

        if (repeat === 'none') {
            return due;
        }

        // If reminder start date/time is in the past, advance to current/next occurrence
        while (due < now && (now - due > 60000)) {
            if (repeat === 'daily') {
                due.setDate(due.getDate() + 1);
            } else if (repeat === 'weekly') {
                due.setDate(due.getDate() + 7);
            } else if (repeat === 'monthly') {
                due.setMonth(due.getMonth() + 1);
            } else if (repeat === 'custom' && reminder.custom_days && reminder.custom_days > 0) {
                due.setDate(due.getDate() + parseInt(reminder.custom_days, 10));
            } else {
                break;
            }
        }

        return due;
    }

    /**
     * Play notification sound using HTMLAudioElement with Web Audio API fallback.
     */
    function playNotificationSound() {
        const audio = new Audio('/sounds/reminder.mp3');
        const playPromise = audio.play();

        if (playPromise !== undefined) {
            playPromise.catch(function (error) {
                console.log('HTML5 Audio autoplay blocked or failed, trying Web Audio API synth fallback:', error);
                playWebAudioChime();
            });
        }
    }

    /**
     * Synthesize pleasant double chime using Web Audio API if audio file autoplay is restricted.
     */
    function playWebAudioChime() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();

            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(523.25, ctx.currentTime); // C5
            gain1.gain.setValueAtTime(0.3, ctx.currentTime);
            gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);

            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start();
            osc1.stop(ctx.currentTime + 0.5);

            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(659.25, ctx.currentTime + 0.15); // E5
            gain2.gain.setValueAtTime(0.3, ctx.currentTime + 0.15);
            gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.65);

            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(ctx.currentTime + 0.15);
            osc2.stop(ctx.currentTime + 0.65);
        } catch (e) {
            console.log('Web Audio API synth unavailable:', e);
        }
    }

    /**
     * Display in-app Bootstrap Modal or Toast popup for due reminder.
     */
    function showInAppPopup(reminder, dueTimeStr) {
        const modalEl = document.getElementById('reminderAlertModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            document.getElementById('reminderAlertTitle').textContent = reminder.title;
            document.getElementById('reminderAlertDesc').textContent = reminder.description || 'No description provided.';
            document.getElementById('reminderAlertTime').textContent = dueTimeStr;
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
            return;
        }

        // Toast fallback
        const toastEl = document.getElementById('reminderToast');
        if (toastEl && typeof bootstrap !== 'undefined') {
            document.getElementById('reminderToastTitle').textContent = reminder.title;
            document.getElementById('reminderToastBody').textContent = (reminder.description ? reminder.description + ' - ' : '') + dueTimeStr;
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    }

    /**
     * Display desktop Notification via Browser Notification API.
     */
    function triggerBrowserNotification(reminder) {
        if (!('Notification' in window)) return;
        if (window.localStorage.getItem('anotes-notifications-enabled') === 'off') return;
        if (Notification.permission === 'granted') {
            const title = 'ANoteS Reminder: ' + reminder.title;
            const options = {
                body: reminder.description || ('Scheduled for ' + reminder.remind_time),
                icon: '/favicon.ico',
                tag: 'anotes-reminder-' + reminder.id
            };
            try {
                new Notification(title, options);
            } catch (e) {
                console.log('Desktop notification error:', e);
            }
        }
    }

    /**
     * Check all active user reminders against current date/time.
     */
    function checkReminders() {
        if (!window.ANOTES_REMINDERS || !Array.isArray(window.ANOTES_REMINDERS)) {
            return;
        }

        const now = new Date();

        window.ANOTES_REMINDERS.forEach(function (reminder) {
            // Skip completed reminders
            if (reminder.is_done) {
                return;
            }

            const due = calculateNextOccurrence(reminder);
            if (!due) {
                return;
            }

            if (window.localStorage.getItem('anotes-notifications-enabled') === 'off') {
                return;
            }

            const diffMs = due.getTime() - now.getTime();
            // Unique occurrence key incorporating reminder ID and due ISO minute
            const occurrenceKey = 'anotes_notif_' + reminder.id + '_' + due.toISOString().slice(0, 16);

            // Check if already notified in memory or sessionStorage
            if (alertedKeys.has(occurrenceKey) || sessionStorage.getItem(occurrenceKey)) {
                return;
            }

            // Trigger when due now or within the last 60 seconds
            if (diffMs <= 0 && diffMs > -60000) {
                // Record occurrence to prevent duplicate notifications
                alertedKeys.add(occurrenceKey);
                try {
                    sessionStorage.setItem(occurrenceKey, '1');
                } catch (e) {
                    // sessionStorage unavailable fallback
                }

                const dueTimeStr = due.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                // 1. In-app popup
                showInAppPopup(reminder, dueTimeStr);

                // 2. Browser Desktop Notification
                triggerBrowserNotification(reminder);

                // 3. Notification Sound
                playNotificationSound();
            }
        });
    }

    /**
     * Initialize notification permission controls and polling loop.
     */
    function init() {
        const enableBtn = document.getElementById('enableNotifsBtn');
        const notifBanner = document.getElementById('notifBlockedBanner');
        const reminderData = document.getElementById('anotes-reminders-data');

        if (!window.ANOTES_REMINDERS && reminderData) {
            try {
                window.ANOTES_REMINDERS = JSON.parse(reminderData.textContent || '[]');
            } catch (error) {
                window.ANOTES_REMINDERS = [];
            }
        }

        function areNotificationsEnabled() {
            return window.localStorage.getItem('anotes-notifications-enabled') !== 'off';
        }

        function setNotificationsEnabled(isEnabled) {
            window.localStorage.setItem('anotes-notifications-enabled', isEnabled ? 'on' : 'off');
        }

        function updatePermissionUI() {
            if (!enableBtn) return;

            if (!('Notification' in window)) {
                enableBtn.disabled = true;
                enableBtn.innerHTML = '<i class="bi bi-bell-slash me-1"></i> Notifications Not Supported';
                return;
            }

            if (Notification.permission === 'granted') {
                if (areNotificationsEnabled()) {
                    enableBtn.innerHTML = '<i class="bi bi-bell-fill me-1"></i> Notifications On';
                    enableBtn.className = 'btn btn-sm btn-success';
                } else {
                    enableBtn.innerHTML = '<i class="bi bi-bell-slash me-1"></i> Notifications Off';
                    enableBtn.className = 'btn btn-sm btn-outline-secondary';
                }
                if (notifBanner) notifBanner.classList.add('d-none');
            } else if (Notification.permission === 'denied') {
                enableBtn.innerHTML = '<i class="bi bi-bell-slash me-1"></i> Notifications Blocked';
                enableBtn.className = 'btn btn-sm btn-outline-danger';
                if (notifBanner) notifBanner.classList.remove('d-none');
            } else {
                enableBtn.innerHTML = '<i class="bi bi-bell me-1"></i> Enable Notifications';
                enableBtn.className = 'btn btn-sm btn-outline-primary';
                if (notifBanner) notifBanner.classList.add('d-none');
            }
        }

        if (enableBtn) {
            enableBtn.addEventListener('click', function () {
                if (!('Notification' in window)) return;

                if (Notification.permission === 'granted') {
                    setNotificationsEnabled(!areNotificationsEnabled());
                    updatePermissionUI();
                    return;
                }

                Notification.requestPermission().then(function () {
                    if (Notification.permission === 'granted') {
                        setNotificationsEnabled(true);
                    }
                    updatePermissionUI();
                });
            });
        }

        updatePermissionUI();

        // Initial check and set 15-second polling interval
        checkReminders();
        setInterval(checkReminders, 15000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
