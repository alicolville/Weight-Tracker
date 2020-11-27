import React from 'react'
import { render } from 'react-dom';

class WeightKg extends React.Component {

  constructor(props) {
    super(props);

    this.state = { value: this.props.value }

    this.handleChange = this.handleChange.bind(this);
  }

  handleChange( event ) {
    this.setState( { value: event.target.value } );
    window.console.log(this.state.value);
    this.props.handleChange( event.target.value );
  }

  render() {

    return( <div className="ws-ls-form-row">
                <input type="number" onChange={ this.handleChange } value={this.state.value} id={this.props.name} name={this.props.name}
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
