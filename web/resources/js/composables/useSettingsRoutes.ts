import { edit as adminAppearance } from '@/routes/admin/appearance';
import {
    edit as adminProfile,
    destroy as adminProfileDestroy,
    update as adminProfileUpdate,
} from '@/routes/admin/profile';
import { show as adminTwoFactor } from '@/routes/admin/two-factor';
import {
    edit as adminPassword,
    update as adminPasswordUpdate,
} from '@/routes/admin/user-password';
import { edit as customerAppearance } from '@/routes/customer/appearance';
import {
    edit as customerProfile,
    destroy as customerProfileDestroy,
    update as customerProfileUpdate,
} from '@/routes/customer/profile';
import { show as customerTwoFactor } from '@/routes/customer/two-factor';
import {
    edit as customerPassword,
    update as customerPasswordUpdate,
} from '@/routes/customer/user-password';
import { User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useSettingsRoutes(userProp?: User) {
    const page = usePage();
    const user = computed(() => userProp || page.props.auth.user);

    const routes = computed(() => {
        const isAdmin = user.value.role === 'admin';

        return {
            profile: {
                edit: isAdmin ? adminProfile : customerProfile,
                update: isAdmin ? adminProfileUpdate : customerProfileUpdate,
                destroy: isAdmin ? adminProfileDestroy : customerProfileDestroy,
            },
            password: {
                edit: isAdmin ? adminPassword : customerPassword,
                update: isAdmin ? adminPasswordUpdate : customerPasswordUpdate,
            },
            twoFactor: {
                show: isAdmin ? adminTwoFactor : customerTwoFactor,
            },
            appearance: {
                edit: isAdmin ? adminAppearance : customerAppearance,
            },
        };
    });

    return routes;
}
