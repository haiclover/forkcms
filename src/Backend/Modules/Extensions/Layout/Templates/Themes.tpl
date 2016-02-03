{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblThemes|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showExtensionsUploadTheme}
        <a href="{$var|geturl:'upload_theme'}" class="btn btn-default">
          <span class="fa fa-upload"></span>&nbsp;
          <span>{$lblUploadTheme|ucfirst}</span>
        </a>
        {/option:showExtensionsUploadTheme}
        <a href="http://www.fork-cms.com/extensions/themes" target="_blank" class="btn btn-default">
          <span class="fa fa-search"></span>&nbsp;
          <span>{$lblFindThemes|ucfirst}</span>
        </a>
      </div>
    </div>
  </div>
</div>
{form:settingsThemes}
  {option:installableThemes}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblInstallableThemes|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <p class="help-block">{$msgHelpInstallableThemes}</p>
          <ul id="installableThemes" class="selectThumbList list-unstyled list-inline">
            {iteration:installableThemes}
            <li>
              <div class="panel panel-default">
                <div class="panel-heading">
                  <label for="{$installedThemes.id}" class="panel-title" class="control-label">
                    <span>{$installableThemes.label|ucfirst}</span>
                  </label>
                </div>
                <div class="panel-body">
                  <img src="{$installableThemes.thumbnail}" width="172" height="129" alt="{$installableThemes.label|ucfirst}" />
                </div>
                <div class="panel-footer">
                  {option:showExtensionsDetailTheme}
                  <div class="btn-toolbar">
                    <div class="btn-group pull-right" role="group">
                      {option:showExtensionsInstallTheme}
                      <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#confirmInstall{$installableThemes.value|ucfirst}">
                        <span class="fa fa-download"></span>&nbsp;
                        {$lblInstall|ucfirst}
                      </button>
                      {/option:showExtensionsInstallTheme}
                      {option:showExtensionsDetailTheme}
                      <a href="{$var|geturl:'detail_theme'}&theme={$installableThemes.value}" class="btn btn-default" role="button" title="{$installableThemes.label|ucfirst}">
                        <span class="fa fa-search"></span>&nbsp;
                        <span>{$lblDetails|ucfirst}</span>
                      </a>
                      {/option:showExtensionsDetailTheme}
                    </div>
                  </div>
                  {option:showExtensionsInstallTheme}
                  <div class="modal fade" id="confirmInstall{$installableThemes.value|ucfirst}" tabindex="-1" role="dialog" aria-labelledby="{$lblInstall|ucfirst}" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <span class="modal-title h4">{$lblInstall|ucfirst}</span>
                        </div>
                        <div class="modal-body">
                          <p>{$msgConfirmThemeInstall}</p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-times"></span> {$lblCancel|ucfirst}</button>
                          <a href="{$var|geturl:'install_theme'}&theme={$installableThemes.value}" class="btn btn-success">
                            <span class="fa fa-check"></span> {$lblOK|ucfirst}
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  {/option:showExtensionsInstallTheme}
                  {/option:showExtensionsDetailTheme}
                </div>
              </div>
            </li>
            {/iteration:installableThemes}
          </ul>
        </div>
      </div>
    </div>
  </div>
  {/option:installableThemes}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblInstalledThemes|ucfirst}&nbsp;
            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
          </h3>
        </div>
        <div class="panel-body">
          <p class="help-block">{$msgHelpThemes}</p>
          {option:rbtInstalledThemesError}
          <p class="text-danger">{$rbtThemesError}</p>
          {/option:rbtInstalledThemesError}
          <div id="installedThemes" class="selectThumbList row">
            {iteration:installedThemes}
            <div class="col-md-4 theme">
              <div class="panel {option:!installedThemes.selected}panel-default{/option:!installedThemes.selected}{option:installedThemes.selected}panel-primary{/option:installedThemes.selected}">
                <div class="panel-heading">
                  <label for="{$installedThemes.id}" class="panel-title">
                    {$installedThemes.rbtInstalledThemes}
                    <span>{$installedThemes.label|ucfirst}</span>
                  </label>
                </div>
                <div class="panel-body">
                  <img src="{$installedThemes.thumbnail}" class="img-thumbnail" alt="{$installedThemes.label|ucfirst}" />
                </div>
                <div class="panel-footer">
                  {option:showExtensionsDetailTheme}
                  <div class="btn-toolbar">
                    <div class="btn-group pull-right" role="group">
                      <a href="{$var|geturl:'detail_theme'}&theme={$installedThemes.value}" class="btn btn-default" role="button"  title="{$installedThemes.label|ucfirst}">
                        <span class="fa fa-search"></span>&nbsp;
                        <span>{$lblDetails|ucfirst}</span>
                      </a>
                    </div>
                  </div>
                  {/option:showExtensionsDetailTheme}
                </div>
              </div>
            </div>
            {/iteration:installedThemes}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="editButton" type="submit" name="edit" class="btn btn-success">
            <span class="fa fa-floppy-o"></span>&nbsp;{$lblSave|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:settingsThemes}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
