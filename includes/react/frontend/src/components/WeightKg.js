import React from 'react'
import { render } from 'react-dom';

class WeightKg extends React.Component {

  constructor(props) {
    super(props);
  }

  render() {

    return( <div className="ws-ls-form-row">
                <input type="number" value={this.props.value} id={this.props.name} name={this.props.name}
                       placeholder={ws_ls_react.locale.kg} step="any"  size="4" min="0" max="9999"/>
    </div>

    );
  }
}

WeightKg.defaultProps = {
  value: '',
  name: 'kg'
}


export default WeightKg;
