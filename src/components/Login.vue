<template>
    <boxed-layout mainClass="login">
        <div slot="header">
            <p class="headline"><strong>Contao Manager</strong></p>
        </div>
        <section slot="section">
            <h1>{{ 'ui.login.headline' | translate }}</h1>
            <p>{{ 'ui.login.description' | translate }}</p>

            <text-field ref="username" name="username" :label="'ui.login.username' | translate" :placeholder="'ui.login.username' | translate" :class="login_failed ? 'invalid' : ''" :disabled="logging_in" v-model="username" @enter="login" @input="reset"></text-field>
            <text-field type="password" name="password" :label="'ui.login.password' | translate" :placeholder="'ui.login.password' | translate" :class="login_failed ? 'invalid' : ''" :disabled="logging_in" v-model="password" @enter="login" @input="reset"></text-field>

            <a href="https://github.com/contao/contao-manager/issues/14" target="_blank">{{ 'ui.login.forgotPassword' | translate }}</a>

            <button class="primary" @click="login" :disabled="!inputValid || logging_in || login_failed">
                <span v-if="!logging_in">{{ 'ui.login.button' | translate }}</span>
                <loader v-else></loader>
            </button>
        </section>
    </boxed-layout>
</template>

<script>
    import store from '../store';
    import apiStatus from '../api/status';
    import routes from '../router/routes';

    import BoxedLayout from './layouts/Boxed';
    import TextField from './widgets/TextField';
    import Loader from './fragments/Loader';

    export default {
        components: { BoxedLayout, TextField, Loader },

        data: () => ({
            username: '',
            password: '',

            logging_in: false,
            login_failed: false,
        }),

        computed: {
            inputValid() {
                return this.username !== '' && this.password !== '' && this.password.length >= 8;
            },
        },

        methods: {
            login() {
                if (!this.inputValid) {
                    return;
                }

                this.logging_in = true;

                this.$store.dispatch('auth/login', {
                    username: this.username,
                    password: this.password,
                })
                .then(() => {
                    if (this.$store.state.auth.username) {
                        this.$router.push(routes.install);
                    } else {
                        this.logging_in = false;
                        this.login_failed = true;
                    }
                });
            },
            reset() {
                this.login_failed = false;
            },
        },

        beforeRouteEnter(to, from, next) {
            store.dispatch('fetchStatus').then((result) => {
                if (result.status === apiStatus.OK) {
                    next(routes.packages);
                }

                next();
            });
        },

        mounted() {
            this.$refs.username.focus();
        },
    };
</script>
