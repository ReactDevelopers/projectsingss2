import * as React from 'react';
import * as ReactDOM from 'react-dom';

interface ReadMoreProps {
    data: string;
    minChar: number;
}




export default class ReadMore extends React.Component<ReadMoreProps> {

    constructor(props: ReadMoreProps) {
        super(props);
    }

    /**
      * Dispaly the view More button in cell
      * @param cell 
      * @param row 
      * @param minLength 
      */
     displayLessMoreAction(cell: undefined | string, minLength?: number ): React.ReactElement<HTMLElement> {

        minLength = minLength === undefined ? 100: minLength;

        let d: string = cell ? cell.toString() : '';
        let needToDisplayLess: boolean = d.length > minLength; 
        
        if(!needToDisplayLess) {
            return <span>{d}</span>
        }
        else{

            let shortData = d.slice(0, minLength);
            return <span data-veiw={1} > 
                <span className="only-text">{shortData}...</span>
                    <span className="table-cell-read-more" 
                        data-shorttext={shortData} 
                        data-text={d} 
                        onClick={ (e: React.FormEvent<Element>) => { this.displayActionData(e) } }> more
                    </span> 
                </span>
        }
    }
    displayActionData(elem: React.FormEvent<Element> ){

        let content = elem.currentTarget.getAttribute('data-text');
        let shortContent = elem.currentTarget.getAttribute('data-shorttext');
        let pNode  = ReactDOM.findDOMNode(elem.currentTarget).parentNode;

        let viewType = $(pNode).attr('data-veiw');

        let newViewType = viewType == 1 ? 2 : 1;
        $(pNode).attr('data-veiw',newViewType);

        if(viewType ==1) {
            
            $(pNode).find('.only-text').html(content);
            $(elem.currentTarget).text(' less');

        } else {

            $(pNode).find('.only-text').html(shortContent+'...');
            $(elem.currentTarget).text(' more');
        }
    }


    render(){
        const {data, minChar} = this.props;
        return this.displayLessMoreAction(data, minChar);
    }
}