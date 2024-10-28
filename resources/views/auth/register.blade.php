@extends('layouts.app')

@section('content')
<div class="container login-aloja">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Registrate</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-12">
                                <h2>Bienvenido a Aloja Colombia</h2>
                                <input placeholder="{{ __('Name') }}" id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input placeholder="Apellido" id="apellido" type="text" class="form-control" name="apellido" value="{{ old('apellido') }}" value="" required>
                            </div>

                                @error('apellido')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input placeholder="{{ __('E-Mail Address') }}" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <select name="country_code" class="form-control login-aloja-select" style="border-radius: 15px !important; border-color: grey !important; color: black !important;">
                                    <option value="+93">+93 (Afghanistan)</option>
                                    <option value="+355">+355 (Albania)</option>
                                    <option value="+21">+21 (Algeria)</option>
                                    <option value="+684">+684 (American Samoa)</option>
                                    <option value="+376">+376 (Andorra)</option>
                                    <option value="+244">+244 (Angola)</option>
                                    <option value="+1-264">+1-264 (Anguilla)</option>
                                    <option value="+1-268">+1-268 (Antigua and Barbuda)</option>
                                    <option value="+54">+54 (Argentina)</option>
                                    <option value="+61">+61 (Australia)</option>
                                    <option value="+43">+43 (Austria)</option>
                                    <option value="+1-242">+1-242 (Bahamas)</option>
                                    <option value="+973">+973 (Bahrain)</option>
                                    <option value="+880">+880 (Bangladesh)</option>
                                    <option value="+1-246">+1-246 (Barbados)</option>
                                    <option value="+375">+375 (Belarus)</option>
                                    <option value="+32">+32 (Belgium)</option>
                                    <option value="+501">+501 (Belize)</option>
                                    <option value="+229">+229 (Benin)</option>
                                    <option value="+1-441">+1-441 (Bermuda)</option>
                                    <option value="+591">+591 (Bolivia)</option>
                                    <option value="+387">+387 (Bosnia and Herzegovina)</option>
                                    <option value="+267">+267 (Botswana)</option>
                                    <option value="+55">+55 (Brazil)</option>
                                    <option value="+1-284">+1-284 (British Virgin Islands)</option>
                                    <option value="+673">+673 (Brunei Darusalaam)</option>
                                    <option value="+359">+359 (Bulgaria)</option>
                                    <option value="+226">+226 (Burkina Faso)</option>
                                    <option value="+257">+257 (Burundi)</option>
                                    <option value="+7">+7 (Byelorussian)</option>
                                    <option value="+855">+855 (Cambodia)</option>
                                    <option value="+237">+237 (Cameroon)</option>
                                    <option value="+1">+1 (Canada)</option>
                                    <option value="+238">+238 (Cape Verde)</option>
                                    <option value="+1-345">+1-345 (Cayman Islands)</option>
                                    <option value="+236">+236 (Central African Republic)</option>
                                    <option value="+235">+235 (Chad)</option>
                                    <option value="+56">+56 (Chile)</option>
                                    <option value="+86">+86 (China)</option>
                                    <option value="+672">+672 (Christmas Island)</option>
                                    <option value="+672">+672 (Cocos Islands)</option>
                                    <option value="+57" selected>+57 (Colombia)</option>
                                    <option value="+1-670">+1-670 (Commonwealth of the Northern Mariana Islands)</option>
                                    <option value="+269">+269 (Comoros and Mayotte Island)</option>
                                    <option value="+242">+242 (Congo)</option>
                                    <option value="+682">+682 (Cook Islands)</option>
                                    <option value="+506">+506 (Costa Rica)</option>
                                    <option value="+385">+385 (Croatia)</option>
                                    <option value="+53">+53 (Cuba)</option>
                                    <option value="+357">+357 (Cyprus)</option>
                                    <option value="+420">+420 (Czech Republic)</option>
                                    <option value="+45">+45 (Denmark)</option>
                                    <option value="+246">+246 (Diego Garcia)</option>
                                    <option value="+253">+253 (Djibouti)</option>
                                    <option value="+1-767">+1-767 (Dominica)</option>
                                    <option value="+1-809">+1-809 (Dominican Republic)</option>
                                    <option value="+62">+62 (East Timor)</option>
                                    <option value="+593">+593 (Ecuador)</option>
                                    <option value="+20">+20 (Egypt)</option>
                                    <option value="+503">+503 (El Salvador)</option>
                                    <option value="+240">+240 (Equatorial Guinea)</option>
                                    <option value="+372">+372 (Estonia)</option>
                                    <option value="+251">+251 (Ethiopia)</option>
                                    <option value="+298">+298 (Faeroe Islands)</option>
                                    <option value="+500">+500 (Falkland Islands)</option>
                                    <option value="+679">+679 (Fiji)</option>
                                    <option value="+358">+358 (Finland)</option>
                                    <option value="+33">+33 (France)</option>
                                    <option value="+590">+590 (French Antilles)</option>
                                    <option value="+594">+594 (French Guiana)</option>
                                    <option value="+689">+689 (French Polynesia)</option>
                                    <option value="+241">+241 (Gabon)</option>
                                    <option value="+220">+220 (Gambia)</option>
                                    <option value="+995">+995 (Georgia)</option>
                                    <option value="+49">+49 (Germany)</option>
                                    <option value="+233">+233 (Ghana)</option>
                                    <option value="+350">+350 (Gibraltar)</option>
                                    <option value="+30">+30 (Greece)</option>
                                    <option value="+299">+299 (Greenland)</option>
                                    <option value="+1-473">+1-473 (Grenada)</option>
                                    <option value="+1-671">+1-671 (Guam)</option>
                                    <option value="+502">+502 (Guatemala)</option>
                                    <option value="+224">+224 (Guinea)</option>
                                    <option value="+245">+245 (Guinea-Bissau)</option>
                                    <option value="+592">+592 (Guyana)</option>
                                    <option value="+509">+509 (Haiti)</option>
                                    <option value="+504">+504 (Honduras)</option>
                                    <option value="+852">+852 (Hong Kong)</option>
                                    <option value="+36">+36 (Hungary)</option>
                                    <option value="+354">+354 (Iceland)</option>
                                    <option value="+91">+91 (India)</option>
                                    <option value="+62">+62 (Indonesia)</option>
                                    <option value="+98">+98 (Iran)</option>
                                    <option value="+964">+964 (Iraq)</option>
                                    <option value="+353">+353 (Irish Republic)</option>
                                    <option value="+972">+972 (Israel)</option>
                                    <option value="+39">+39 (Italy)</option>
                                    <option value="+225">+225 (Ivory Coast)</option>
                                    <option value="+1-876">+1-876 (Jamaica)</option>
                                    <option value="+81">+81 (Japan)</option>
                                    <option value="+962">+962 (Jordan)</option>
                                    <option value="+254">+254 (Kenya)</option>
                                    <option value="+686">+686 (Kiribati Republic)</option>
                                    <option value="+965">+965 (Kuwait)</option>
                                    <option value="+856">+856 (Laos)</option>
                                    <option value="+371">+371 (Latvia)</option>
                                    <option value="+961">+961 (Lebanon)</option>
                                    <option value="+266">+266 (Lesotho)</option>
                                    <option value="+231">+231 (Liberia)</option>
                                    <option value="+21">+21 (Libya)</option>
                                    <option value="+41">+41 (Liechtenstein)</option>
                                    <option value="+370">+370 (Lithuania)</option>
                                    <option value="+352">+352 (Luxembourg)</option>
                                    <option value="+853">+853 (Macao)</option>
                                    <option value="+389">+389 (Macedonia)</option>
                                    <option value="+261">+261 (Madagascar)</option>
                                    <option value="+265">+265 (Malawi)</option>
                                    <option value="+60">+60 (Malaysia)</option>
                                    <option value="+960">+960 (Maldives)</option>
                                    <option value="+223">+223 (Mali)</option>
                                    <option value="+356">+356 (Malta)</option>
                                    <option value="+692">+692 (Marshall Islands)</option>
                                    <option value="+596">+596 (Martinique)</option>
                                    <option value="+222">+222 (Mauritania)</option>
                                    <option value="+230">+230 (Mauritius)</option>
                                    <option value="+1-706">+1-706 (Mexico)</option>
                                    <option value="+691">+691 (Micronesia)</option>
                                    <option value="+33">+33 (Monaco)</option>
                                    <option value="+976">+976 (Mongolia)</option>
                                    <option value="+1-664">+1-664 (Montserrat)</option>
                                    <option value="+212">+212 (Morocco)</option>
                                    <option value="+258">+258 (Mozambique)</option>
                                    <option value="+95">+95 (Myanmar)</option>
                                    <option value="+264">+264 (Namibia)</option>
                                    <option value="+674">+674 (Nauru)</option>
                                    <option value="+977">+977 (Nepal)</option>
                                    <option value="+31">+31 (Netherlands)</option>
                                    <option value="+599">+599 (Netherlands Antilles)</option>
                                    <option value="+687">+687 (New Caledonia)</option>
                                    <option value="+64">+64 (New Zealand)</option>
                                    <option value="+505">+505 (Nicaragua)</option>
                                    <option value="+227">+227 (Niger)</option>
                                    <option value="+234">+234 (Nigeria)</option>
                                    <option value="+683">+683 (Niue)</option>
                                    <option value="+672">+672 (Norfolk Island)</option>
                                    <option value="+850">+850 (North Korea)</option>
                                    <option value="+967">+967 (North Yemen)</option>
                                    <option value="+670">+670 (Northern Mariana Islands)</option>
                                    <option value="+47">+47 (Norway)</option>
                                    <option value="+968">+968 (Oman)</option>
                                    <option value="+92">+92 (Pakistan)</option>
                                    <option value="+507">+507 (Panama)</option>
                                    <option value="+675">+675 (Papua New Guinea)</option>
                                    <option value="+595">+595 (Paraguay)</option>
                                    <option value="+51">+51 (Peru)</option>
                                    <option value="+63">+63 (Philippines)</option>
                                    <option value="+48">+48 (Poland)</option>
                                    <option value="+351">+351 (Portugal)</option>
                                    <option value="+1-787">+1-787 (Puerto Rico)</option>
                                    <option value="+974">+974 (Qatar)</option>
                                    <option value="+378">+378 (Republic of San Marino)</option>
                                    <option value="+262">+262 (Reunion)</option>
                                    <option value="+40">+40 (Romania)</option>
                                    <option value="+7">+7 (Russia)</option>
                                    <option value="+250">+250 (Rwandese Republic)</option>
                                    <option value="+247">+247 (Saint Helena and Ascension Island)</option>
                                    <option value="+508">+508 (Saint Pierre et Miquelon)</option>
                                    <option value="+39">+39 (San Marino)</option>
                                    <option value="+239">+239 (Sao Tome e Principe)</option>
                                    <option value="+966">+966 (Saudi Arabia)</option>
                                    <option value="+221">+221 (Senegal)</option>
                                    <option value="+248">+248 (Seychelles)</option>
                                    <option value="+232">+232 (Sierra Leone)</option>
                                    <option value="+65">+65 (Singapore)</option>
                                    <option value="+421">+421 (Slovakia)</option>
                                    <option value="+386">+386 (Slovenia)</option>
                                    <option value="+677">+677 (Solomon Islands)</option>
                                    <option value="+252">+252 (Somalia)</option>
                                    <option value="+27">+27 (South Africa)</option>
                                    <option value="+82">+82 (South Korea)</option>
                                    <option value="+969">+969 (South Yemen)</option>
                                    <option value="+34">+34 (Spain)</option>
                                    <option value="+94">+94 (Sri Lanka)</option>
                                    <option value="+1-869">+1-869 (St. Kitts and Nevis)</option>
                                    <option value="+1-758">+1-758 (St. Lucia)</option>
                                    <option value="+1-784">+1-784 (St. Vincent and the Grenadines)</option>
                                    <option value="+249">+249 (Sudan)</option>
                                    <option value="+597">+597 (Suriname)</option>
                                    <option value="+47">+47 (Svalbard and Jan Mayen Islands)</option>
                                    <option value="+268">+268 (Swaziland)</option>
                                    <option value="+46">+46 (Sweden)</option>
                                    <option value="+41">+41 (Switzerland)</option>
                                    <option value="+963">+963 (Syria)</option>
                                    <option value="+886">+886 (Taiwan)</option>
                                    <option value="+255">+255 (Tanzania)</option>
                                    <option value="+66">+66 (Thailand)</option>
                                    <option value="+228">+228 (Togolese Republic)</option>
                                    <option value="+690">+690 (Tokelau)</option>
                                    <option value="+676">+676 (Tonga)</option>
                                    <option value="+1-868">+1-868 (Trinidad and Tobago)</option>
                                    <option value="+21">+21 (Tunisia)</option>
                                    <option value="+90">+90 (Turkey)</option>
                                    <option value="+1-649">+1-649 (Turks)</option>
                                    <option value="+688">+688 (Tuvalu)</option>
                                    <option value="+1-340">+1-340 (US Virgin Islands)</option>
                                    <option value="+256">+256 (Uganda)</option>
                                    <option value="+380">+380 (Ukraine)</option>
                                    <option value="+971">+971 (United Arab Emirates)</option>
                                    <option value="+44">+44 (United Kingdom)</option>
                                    <option value="+1">+1 (United States of America)</option>
                                    <option value="+598">+598 (Uruguay)</option>
                                    <option value="+678">+678 (Vanuatu)</option>
                                    <option value="+39">+39 (Vatican City)</option>
                                    <option value="+58">+58 (Venezuela)</option>
                                    <option value="+84">+84 (Vietnam)</option>
                                    <option value="+681">+681 (Wallis and Futuna Islands)</option>
                                    <option value="+21">+21 (Western Sahara)</option>
                                    <option value="+685">+685 (Western Samoa)</option>
                                    <option value="+381">+381 (Yugoslavia)</option>
                                    <option value="+243">+243 (Zaire)</option>
                                    <option value="+260">+260 (Zambia)</option>
                                    <option value="+263">+263 (Zimbabwe)</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <input placeholder="Celular" id="celular" type="tel" class="form-control" name="celular" pattern="^\+?[0-9]*$" value="+57" required>
                            </div>

                                @error('celular')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input placeholder="{{ __('Password') }}" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                    
                                <small class="form-text text-muted">Mínimo 10 caracteres. Al menos una letra minúscula, una mayúscula y un número.</small>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input placeholder="{{ __('Confirm Password') }}" id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terminos" id="terminos" required>
                                    <label class="form-check-label" for="terminos">Acepto</label>&nbsp;{!! link_to('/doc/terminos.pdf', 'Términos y Condiciones', ['target' => '_blank', 'class' => 'pie-link2' ]) !!}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privacidad" id="privacidad" required>
                                    <label class="form-check-label" for="privacidad">Acepto</label>&nbsp;{!! link_to('/doc/privacidad.pdf', 'Política de Privacidad', ['target' => '_blank', 'class' => 'pie-link2' ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    Crear cuenta
                                </button>
                            </div>
                        </div>
                    </form>


                    <br/>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
