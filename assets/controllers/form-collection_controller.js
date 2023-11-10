import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['collectionContainer']

    static values = {
        index: Number,
        prototype: String,
    }

    addCollectionElement(event)
    {
        const item = document.createElement('div');
        this.collectionContainerTarget.appendChild(item);
        item.setAttribute('class', 'form-trick-picture');
        item.setAttribute('data-controller', 'picture');
        item.setAttribute('data-picture-target', 'picture');
            
        const newFormPicture = this.prototypeValue.replace(/__name__/g, this.indexValue);
        item.innerHTML = `
            ${ newFormPicture }
            <div class="button" data-action="click->picture#deletePicture">
                Supprimer l'image
            </div>
        `
        this.indexValue++;
    }
}