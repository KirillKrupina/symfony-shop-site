<template>
    <div class="row mb-2">
        <div class="col-md-2">
            <select
                    v-model="form.categoryId"
                    name="add_product_category_select"
                    class="form-control"
                    @change="getProducts()"
            >
                <option value="" disabled>- Choose option -</option>
                <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                >
                    {{category.title}}
                </option>
            </select>
        </div>
        <div v-if="form.categoryId" class="col-md-3">
            <select
                    v-model="form.productId"
                    name="add_product_product_select"
                    class="form-control"
                    @change="() => {
                    	this.form.pricePerOne = productPrice
                    }"
            >
                <option value="" disabled>- Choose option -</option>
                <option
                        v-for="product in freeProductsByCategory"
                        :key="product.id"
                        :value="product.uuid"
                >
                    {{formatProductInfo(product)}}
                </option>
            </select>
        </div>
        <div v-if="form.productId" class="col-md-2">
            <input
                    v-model="form.quantity"
                    type="number"
                    class="form-control"
                    placeholder="Quantity"
                    min="1"
                    :max="productQuantityMax"
                    @change="() => {
                    	if (form.quantity) {
                    		if(form.quantity > productQuantityMax) {
                    		    this.form.quantity = productQuantityMax
                    	    } else if (form.quantity < 1) {
                    		    this.form.quantity = 1
                    	    }
                    	}

                    }"
            />
        </div>
        <div v-if="form.productId" class="col-md-2">
            <input
                    v-model="form.pricePerOne"
                    type="number"
                    class="form-control"
                    placeholder="Price per one"
                    :readonly="true"
            />
        </div>
        <div v-if="form.productId" class="col-md-3">
            <button
                    class="btn btn-outline-success"
                    @click="submit"
            >
                Add
            </button>
        </div>
    </div>
</template>

<script>
	import {mapActions, mapGetters, mapMutations, mapState} from "vuex";
	import {getUrlViewProduct} from "../../../../utils/urlGenerator";

	export default {
		name: 'OrderProductAdd',
		data() {
			return {
				form: {
					categoryId: '',
					productId: '',
					quantity: '',
					pricePerOne: ''
				}
			};
		},
		computed: {
			...mapState('products', [
				'categories',
				'productsByCategory',
				'staticStore'
			]),
			...mapGetters('products', ['freeProductsByCategory']),
			productQuantityMax() {
				const productData = this.freeProductsByCategory.find(
					product => product.uuid === this.form.productId
				);
				if (productData) {
					return parseInt(productData.quantity);
				}
			},
			productPrice() {
				const productData = this.freeProductsByCategory.find(
					product => product.uuid === this.form.productId
				);
				if (productData) {
					return productData.price;
				}
			},
		},
		methods: {
			...mapMutations('products', ['setNewProductInfo']),
			...mapActions('products', [
				'getProductsByCategory',
				'addNewOrderProduct'
			]),
			formatProductInfo(product) {
				return (
					'#' + product.id + ' '
					+ product.title
					+ ' / P: $' + product.price
					+ ' / Q: ' + product.quantity
				);
			},
			getProducts() {
				this.setNewProductInfo(this.form);
				this.getProductsByCategory();
			},
			submit(event) {
				event.preventDefault();
				this.setNewProductInfo(this.form);
				this.addNewOrderProduct();
				this.resetProductFormData();
			},
			resetProductFormData() {
				Object.assign(this.$data, this.$options.data.apply(this));
			}
		}
	}
</script>