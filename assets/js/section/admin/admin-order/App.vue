<template>
    <div class="table-additional-selection">
        <OrderProductAdd/>
        <hr/>
        <PulseLoader :loading="!isLoaded" color="#36b9cc" class="text-center"/>
        <OrderProductItem
                v-if="isLoaded"
                v-for="(orderProduct, index) in orderProducts"
                :key="orderProduct.id"
                :order-product="orderProduct"
                :index="index"
        />
        <hr/>
        <OrderTotalPrice/>
    </div>
</template>

<script>
	import {mapActions, mapState} from "vuex";
	import OrderProductItem from "./components/OrderProductItem";
	import OrderProductAdd from "./components/OrderProductAdd";
	import OrderTotalPrice from "./components/OrderTotalPrice";

	import PulseLoader from 'vue-spinner/src/PulseLoader.vue'

	export default {
		components: {OrderProductItem, OrderProductAdd, OrderTotalPrice, PulseLoader},
		created() {
			this.getCategories();
			this.getOrderProducts();
		},
		computed: {
			...mapState('products', [
				'orderProducts', 'isLoaded'
			]),
		},
		methods: {
			...mapActions('products', [
				'getCategories',
				'getOrderProducts'
			])
		}
	}
</script>