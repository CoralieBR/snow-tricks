import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["collectionContainer"]

    static values = {
        index: Number,
        prototype: String,
    }

    addCollectionElement(event)
    {
        const item = document.createElement('div.form-trick-pictures');
        const newFormPicture = this.prototypeValue.replace(/__name__/g, this.indexValue);
        item.innerHTML = `
            <div class="form-trick-picture" data-controller="picture" data-picture-target="picture" data-form-collection-target="picture">
                <img data-picture-target="currentPicture" src="" alt="">
                <div>
                    ${ newFormPicture }
                    <div class="button" data-action="form-collection#deletePicture">
                        Supprimer l'image
                    </div>
                </div>
            </div>
        `
        this.collectionContainerTarget.appendChild(item);
        this.indexValue++;
    }
}