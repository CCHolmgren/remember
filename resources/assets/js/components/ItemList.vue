<template>
    <div>
        <div v-show="!showEditor">
            <div class="panel panel-default">
                <div class="panel-heading">Filter based on type</div>
                <div class="panel-body">
                    <div>
                        <button class="btn" :class="filterType == null ? 'btn-secondary':'btn-default'" @click="resetFilter()">Show all</button>
                        <button v-for="type in types" class="btn" :class="filterType == type ? 'btn-secondary':'btn-default'" @click="filterByType(type)">{{ type }}
                        </button>
                    </div>
                </div>
            </div>
            <item-show v-for="item in computedItems" :key="item.id" :item="item" @deleteditem="deleteItem"
                   @edititem="editItem" @saveditem="savedItem" @update-items="update"></item-show>
        </div>

        <div class="form-group">
            <button class="btn btn-default" @click="showEditor = !showEditor">{{ showEditor ? 'Cancel' : 'New item' }}
            </button>
        </div>
        <div v-show="showEditor">
            <div class="fs-20">Create new item</div>
            <item-creator :item="item" @saveditem="fetchItems"></item-creator>
        </div>
    </div>
</template>
<script>
    import ItemCreator from "./ItemCreator.vue";
    import ItemShow from "./ItemShow.vue";

    export default{
        props: ['items'],
        data() {
            return {
                item: null,
                itemArray: this.items,
                showEditor: false,
                filterType: null
            }
        },
        computed: {
            types() {
                return this.itemArray
                    .map(item => item.type)
                    .filter((item, index, self) => self.indexOf(item) === index)
            },
            computedItems() {
                if(this.filterType) {
                    return this.itemArray.filter(item => item.type == this.filterType)
                }
                return this.itemArray
            }
        },
        methods: {
            fetchItems() {
                this.update();
                this.showEditor = false;
            },
            deleteItem(item) {
                this.itemArray = this.itemArray.filter(i => i.id !== item.id)
            },
            editItem(item) {
                this.$emit('setitem', item);
                this.showEditor = true;
            },
            resetFilter() {
                this.filterType = null;
            },
            filterByType(type) {
                this.filterType = type
            },
            savedItem(item) {
                const index = this.itemArray.findIndex(i => i.id == item.id);
                this.itemArray.splice(index, 1, item);
            },
            update() {
                axios.get('/items').then(response => {
                    this.itemArray = response.data.items
                })
            }
        },
        components:{
            'item-creator':ItemCreator,
            'item-show': ItemShow
        }
    }

</script>
