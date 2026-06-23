@props(['id' => 'global-modal'])

<div x-data="{ 
    open: false, 
    type: 'confirm', 
    message: '', 
    resolve: null,
    confirmText: 'Confirmer',
    cancelText: 'Annuler'
}" 
     x-on:open-modal.window="open = true; type = $event.detail.type; message = $event.detail.message; resolve = $event.detail.resolve; 
        if(type === 'confirm') { confirmText = $event.detail.confirmText || 'Confirmer'; cancelText = $event.detail.cancelText || 'Annuler'; }
        else { confirmText = 'OK'; cancelText = ''; }"
     x-show="open"
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     x-cloak>
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" @click.stop>
        <h3 class="text-lg font-display font-semibold mb-3" x-text="type === 'confirm' ? 'Confirmation' : 'Erreur'"></h3>
        <p class="text-on-surface-variant mb-6" x-text="message"></p>
        <div class="flex justify-end gap-3" :class="type === 'error' ? 'justify-center' : ''">
            <template x-if="type === 'confirm' && cancelText">
                <button @click="open = false; if(resolve) resolve(false)" 
                        class="px-4 py-2 rounded-lg border border-outline/30 text-on-surface-variant hover:bg-surface-low transition"
                        x-text="cancelText"></button>
            </template>
            <button @click="open = false; if(resolve) resolve(type === 'confirm' ? true : null)" 
                    class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary-container transition"
                    x-text="confirmText"></button>
        </div>
    </div>
</div>