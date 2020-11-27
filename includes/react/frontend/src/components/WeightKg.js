import React from 'react'
import { render } from 'react-dom';

class WeightKg extends React.Component {

  constructor(props) {
    super(props);

    this.state = { kg: this.props.value }

    this.handleChange = this.handleChange.bind(this);
  }

  handleChange( e ) {

    this.setState( { kg: e.target.value } );

    // Throw new weight to parent to process
    this.props.handleChange( { format : 'kg', kg : this.state.kg } );
  }

  render() {

    return( <div className="ws-ls-form-row">
                <input type="number" onChange={ this.handleChange } value={this.state.kg} id={this.props.name} name={this.props.name}
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
