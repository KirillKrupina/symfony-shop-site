<template>
    <div class="row">
        <div class="col-lg-12 order-block">
            <div class="order-content">
                <Alert/>

                <div v-if="showCartContent">
                    <CartProductList/>
                    <CartTotalPrice/>
                    <a
                            class="btn btn-success mb-3 text-white"
                            @click="makeOrder"
                    >
                        Make order
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import CartProductList from './components/CartProductList'
	import CartTotalPrice from './components/CartTotalPrice'
	import {mapActions, mapState} from "vuex";
	import Alert from "./components/Alert";

	export default {
		name: "App",
		components: {Alert, CartProductList, CartTotalPrice},
		created() {
			this.getCart();
		},
		computed: {
			...mapState('cart', ['isSendForm', 'cart']),
			showCartContent() {
				return !this.isSendForm && Object.keys(this.cart).length;
			}
		},
		methods: {
			...mapActions('cart', ['getCart', 'makeOrder'])
		}
	}
</script>

<style scoped>

</style>