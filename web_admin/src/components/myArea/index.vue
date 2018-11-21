<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <select v-model="f.p" @change="selpro" title="省份">
                    <option :value="i" v-for="(v,i) in pro">{{v.name}}</option>
                </select>
            </div>

            <div class="col">
                <select v-model="f.c" @change="selcity" title="城市">
                    <option :value="i" v-for="(v,i) in city">{{v.name}}</option>
                </select>
            </div>

            <div class="col">
                <select v-model="f.cc" v-show="county.length>0" @change="result" title="区/县">
                    <option :value="i" v-for="(v,i) in county">{{v.name}}</option>
                </select>
            </div>

        </div>

    </div>
</template>

<style scoped>
    .row {
        display: flex;
    }

    .col {
        flex-grow: 1;
    }

    select {
        width: 100%;
        padding: 0;
        border: 1px solid #eee;
        border-radius: .3rem;
        font-size: 14px;
        margin-bottom: 2px;
    }
</style>

<script>
    /* eslint-disable eqeqeq */

    import data from './data';

    export default {
        props: {
            init: {
                type: Array,
                default: [2, 52, 500]
            }
        },
        data() {
            return {
                data,
                pro: [],
                city: [],
                county: [],
                f: { // 选中的数组下标
                    p: 0,
                    c: 0,
                    cc: 0
                }
            }
        },
        watch: {
            init() {
                this.initSelect();
            }
        },
        created() {
            this.initSelect();
        },
        methods: {
            initSelect() {
                this.pro = this.data;

                for (let i0 = 0; i0 < this.pro.length; i0++) {
                    if (this.pro[i0].id == this.init[0]) { // string == int
                        this.f.p = i0;
                    }
                }
                this.city = this.pro[this.f.p]['child'];

                for (let i1 = 0; i1 < this.city.length; i1++) {
                    if (this.city[i1].id == this.init[1]) { // string == int
                        this.f.c = i1;
                    }
                }
                this.county = this.city[this.f.c]['child'];

                for (let i2 = 0; i2 < this.county.length; i2++) {
                    if (this.county[i2].id == this.init[2]) { // string == int
                        this.f.cc = i2;
                    }
                }

                this.result();
            },
            selpro() {
                this.city = this.pro[this.f.p]['child'];
                this.county = this.city[0]['child'];
                this.f.c = 0;
                this.f.cc = 0;
                this.result();
            },
            selcity() {
                this.county = this.city[this.f.c]['child'];
                this.f.cc = 0;
                this.result();
            },
            result() {
                const re = {
                    pro: { id: this.pro[this.f.p].id, name: this.pro[this.f.p].name },
                    city: { id: this.city[this.f.c].id, name: this.city[this.f.c].name },
                    county: { id: this.county[this.f.cc].id, name: this.county[this.f.cc].name }
                };
                this.$emit('select', re);
            }
        }
    }
</script>