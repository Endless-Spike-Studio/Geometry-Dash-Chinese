import {defineStore} from "pinia";
import {darkTheme, useOsTheme} from "naive-ui";
import {computed} from "vue";
import {useProp} from "@/scripts/core/utils";
import {App} from "@/scripts/types/backend";

export const useAppStore = defineStore('app', {
    state: () => ({
        theme: computed(() => {
            return useOsTheme().value === 'light' ? null : darkTheme;
        })
    })
});

export const useGeometryDashChineseServerStore = defineStore('GDCS', {
    state: () => ({
        account: useProp<App.Models.GDCS.Account>('gdcs.account'),
        user: useProp<App.Models.GDCS.User>('gdcs.user'),
    }),
    getters: {
        logged(store) {
            return store.account !== undefined;
        }
    }
});
