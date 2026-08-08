{include file="frontend/components/header.tpl" pageTitleTranslated=$profileName}
<style>
.emp-profile{max-width:980px;margin:0 auto 3rem}.emp-card{display:flex;gap:1.5rem;align-items:flex-start;background:#fff;border:1px solid #ddd;border-radius:14px;padding:1.5rem;box-shadow:0 2px 10px rgba(0,0,0,.04)}.emp-photo,.emp-photo-fallback{width:150px;height:150px;border-radius:50%;object-fit:cover;flex:0 0 150px;background:#f1f1f1}.emp-photo-fallback{display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:#666}.emp-title{margin:.1rem 0 .5rem}.emp-roles{display:flex;flex-wrap:wrap;gap:.4rem;margin:.25rem 0 .75rem}.emp-role{border:1px solid #d6c8b7;border-radius:999px;padding:.25rem .65rem;font-size:.9rem;color:#5a1213}.emp-affiliation{font-weight:600;margin:.5rem 0}.emp-links{display:flex;flex-wrap:wrap;gap:.5rem;margin:1rem 0}.emp-links a,.emp-back{display:inline-block;text-decoration:none;border:1px solid #d6c8b7;border-radius:999px;padding:.35rem .75rem;color:#5a1213;background:#fff}.emp-links a:hover,.emp-links a:focus,.emp-back:hover,.emp-back:focus{background:#5a1213;color:#fff}.emp-bio{margin-top:1.5rem;line-height:1.6}.emp-bio h2{font-size:1.25rem}.emp-bio-content{overflow-wrap:anywhere}.emp-back-wrap{margin:1.5rem 0}@media(max-width:650px){.emp-card{flex-direction:column;text-align:center;align-items:center}.emp-links,.emp-roles{justify-content:center}}
</style>
<div class="page emp-profile">
    {include file="frontend/components/breadcrumbs.tpl" currentTitle=$profileName}
    <div class="emp-back-wrap"><a class="emp-back" href="{$editorialMastheadUrl|escape}">← Szerkesztői gárda</a></div>
    <article class="emp-card">
        {if $profileImageUrl}
            <img class="emp-photo" src="{$profileImageUrl|escape}" alt="{$profileName|escape}" width="150" height="150" loading="lazy" decoding="async">
        {else}
            <div class="emp-photo-fallback" aria-hidden="true">{$profileName|truncate:1:"":true|escape}</div>
        {/if}
        <div class="emp-main">
            <h1 class="emp-title">{$profileName|escape}</h1>
            {if $profileRoles|@count}
                <div class="emp-roles">
                    {foreach from=$profileRoles item=role}<span class="emp-role">{$role|escape}</span>{/foreach}
                </div>
            {/if}
            {if $profileAffiliation}<div class="emp-affiliation">{$profileAffiliation|escape}</div>{/if}
            <div class="emp-links">
                {if $profileOrcid}<a href="{$profileOrcid|escape}" target="_blank" rel="noopener">ORCID</a>{/if}
                {if $profileUrl}<a href="{$profileUrl|escape}" target="_blank" rel="noopener">Weboldal</a>{/if}
            </div>
        </div>
    </article>
    {if $profileBiography}
        <section class="emp-bio">
            <h2>Bemutatkozás</h2>
            <div class="emp-bio-content">{$profileBiography|strip_unsafe_html}</div>
        </section>
    {/if}
</div>
{include file="frontend/components/footer.tpl"}
