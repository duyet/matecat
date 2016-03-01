export default React.createClass({
    getInitialState : function() {
        
        return {
            issue : MateCat.db
                .segment_translation_issues.by('id', this.props.issueId )
        }
    },

    categoryLabel : function() {
        var id_category = this.state.issue.id_category ; 

        return _( JSON.parse( config.lqa_flat_categories ))
            .select(function(e) { return  e.id == id_category ; })
            .first().label
    },

    deleteIssue : function(event) {
        event.preventDefault();
        event.stopPropagation();
        ReviewImproved.deleteIssue(this.state.issue);
    },
    render : function() {
        var category_label = this.categoryLabel();
        var formatted_date = moment( this.state.issue.created_at ).format('lll'); 

        var commentLine = null; 
        var comment = this.state.issue.comment ; 

        if ( comment ) {
            commentLine = <div className="review-issue-thread-entry">
            <strong>Comment:</strong> { comment }</div>; 
        }

        var deleteIssue ;

        if ( config.isReview ) {
            deleteIssue = <a href="#" onClick={this.deleteIssue}>delete issue</a>;
        }

        return <div className="review-issue-detail"
            onMouseEnter={this.props.issueMouseEnter.bind(null, this.state.issue) }
            onMouseLeave={this.props.issueMouseLeave} >
            <strong>Issue # {this.props.index} </strong>

            <span className="review-issue-severity">{this.state.issue.severity}</span>
            -
            <span className="review-issue-label">{category_label} </span>
            -
            <span className="review-issue-date">{formatted_date} </span>
            <div className="review-issue-comment">
            {commentLine}
            </div>

            {deleteIssue}

            <ReviewTranslationIssueCommentsContainer
                sid={this.props.sid}
                issueId={this.props.issueId} />
        </div>; 
    }
});
