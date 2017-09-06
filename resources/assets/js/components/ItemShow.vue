<template>
    <div class="panel panel-default">
        <div class="panel-body relative">
            <div>
                <b style="font-size: 2.2rem">{{ item.name }}</b>: {{ item.type }}
                <span class="bottom-centered absolute">
                    <span v-show="item.content.length" @click="toggleDetails()" class="pointer glyphicon"
                      :class="show ? 'glyphicon-menu-up' : 'glyphicon-menu-down'" aria-hidden="true"></span>
                </span>

                <span class="pull-right">
                    <button class="btn btn-styleless" data-toggle="tooltip" data-placement="top" title="Copy" @click="copy"><span class="glyphicon glyphicon-copy"></span></button>
                    <button class="btn btn-styleless" data-toggle="tooltip" data-placement="top" title="Edit" @click="toggleEditForm(item)"><span class="glyphicon glyphicon-edit"></span></button>
                    <button class="btn btn-styleless" data-toggle="tooltip" data-placement="top" title="Delete" @click="toggleDeleteForm()"><span class="glyphicon glyphicon-remove"></span></button>
                </span>
            </div>
            <div v-show="showConfirmDelete" style="padding-top: 20px; padding-bottom: 25px;">
                <p class="text-center">Are you sure you want to delete {{ item.name }}?</p>
                <div class="text-center">
                    <button class="btn btn-default" style="margin-right: 25px;" @click="hideConfirmDelete()">Cancel</button>
                    <button class="btn btn-danger" @click="deleteItem(item)">Delete</button>
                </div>
            </div>

            <div v-show="show">
                <div v-for="row in item.content">
                    <b>{{ row.key }}</b>: {{ row.value }}
                </div>
            </div>

            <div v-if="showEdit" style="padding-top: 20px;">
                <item-creator :item="item" @saveditem="savedItem"></item-creator>
            </div>
        </div>
    </div>
</template>
<script>
    import ItemCreator from "./ItemCreator.vue";

    export default{
        props: ['item'],
        data(){
            return {
                show: false,
                showConfirmDelete: false,
                showEdit: false
            }
        },
        methods: {
            savedItem(item) {
                this.showEdit = false;
                this.$emit('saveditem', item);
            },
            toggleDetails() {
                if(this.show === true) {
                    this.showEdit = false;
                    this.showConfirmDelete = false;
                }
                this.show = !this.show;
            },
            toggleEditForm(item) {
                this.showEdit = !this.showEdit;
                this.hideConfirmDelete()
            },
            deleteItem(item) {
                axios.delete(`/items/${item.id}/`).then(response => {
                    console.log("Emitting deleted-item", item);
                    this.$emit('deleteditem', item);
                })
            },
            toggleDeleteForm() {
                this.showConfirmDelete = !this.showConfirmDelete;
                this.showEdit = false
            },
            hideConfirmDelete() {
                this.showConfirmDelete = false
            },
            copy() {
                axios.post(`/items/copy/${this.item.id}`).then(response => {
                    this.$emit('update-items')
                })
            }
        },
        components: {
            'item-creator': ItemCreator
        }
    }

</script>
